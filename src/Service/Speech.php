<?php

namespace Althingi\Service;

use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Events\{UpdateEvent, AddEvent};
use Althingi\Presenters\IndexableSpeechPresenter;
use Althingi\Injector\{EventsAwareInterface, DatabaseAwareInterface};
use Althingi\Model\KindEnum;
use Generator;
use PDO;

class Speech implements DatabaseAwareInterface, EventsAwareInterface
{
    use DatabaseService;
    use EventService;

    const MAX_ROW_COUNT = '18446744073709551615';

    public function get(string $id): ? Model\Speech
    {
        $statement = $this->getDriver()->prepare(
            'select * from `Speech` where speech_id = :speech_id'
        );
        $statement->execute(['speech_id' => $id]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new Hydrator\Speech())->hydrate($object, new Model\Speech())
            : null ;
    }

    public function getLastActive(): ? Model\Speech
    {
        $statement = $this->getDriver()->prepare(
            'select * from `Speech` where `text` is not null order by `from` desc;'
        );
        $statement->execute([]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new Hydrator\Speech())->hydrate($object, new Model\Speech())
            : null ;
    }

    /**
     * This makes two queries, one that for a single congressman will count time
     * for each type of speech. The second one will count time for congressman type.
     *
     *  type                 total
     *  --------------------------
     *  flutningsræða        27115
     *  andsvar              16692
     *  ræða                 8650
     *  svar                 3201
     *  um atkvæðagreiðslu   1486
     *  grein fyrir atkvæði   940
     *
     *
     *
     *  congressman_type                total
     *  -------------------------------------
     *  fjármála- og efnahagsráðherra   58084
     *
     */
    public function getFrequencyByAssemblyAndCongressman(int $assemblyId, int $congressmanId): \stdClass
    {
        $speechTypeStatement = $this->getDriver()->prepare('
            select `type`, sum(`diff`) as `total` from (
                select S.`type`, S.`congressman_type`, TIMESTAMPDIFF(SECOND, S.`from`, S.`to`) as `diff`
                from `Speech` S
                where S.`assembly_id` = :assembly_id and S.`congressman_id` = :congressman_id
            ) as D
            group by `type`
            order by `total` desc;
        ');
        $speechTypeStatement->execute([
            'assembly_id' => $assemblyId,
            'congressman_id' => $congressmanId
        ]);

        $congressmanTypeStatement = $this->getDriver()->prepare('
            select `congressman_type`, sum(`diff`) as `total` from (
                select S.`type`, S.`congressman_type`, TIMESTAMPDIFF(SECOND, S.`from`, S.`to`) as `diff`
                from `Speech` S
                where S.`assembly_id` = :assembly_id and S.`congressman_id` = :congressman_id
            ) as D
            group by `congressman_type`
            order by `total` desc;
        ');
        $congressmanTypeStatement->execute([
            'assembly_id' => $assemblyId,
            'congressman_id' => $congressmanId
        ]);

        return (object) [
            'type' => array_map(function ($speech) {
                $speech->total = (int) $speech->total;
                return $speech;
            }, $speechTypeStatement->fetchAll()),
            'congressman_type' => array_map(function ($speech) {
                $speech->congressman_type = $speech->congressman_type
                    ? $speech->congressman_type
                    : 'þingmaður';
                $speech->total = (int) $speech->total;
                return $speech;
            }, $congressmanTypeStatement->fetchAll()),
        ];
    }

    public function fetchAllByIssue(int $assemblyId, int $issueId, KindEnum $kind = KindEnum::A)
    {
        $statement = $this->getDriver()->prepare("
            select *, timestampdiff(SECOND, `from`, `to`) as `time`
            from `Speech`
            where assembly_id = :assembly_id and issue_id = :issue_id and `kind` = :kind
            order by `from`
        ");

        $statement->execute([
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'kind' => $kind->value
        ]);
        $speeches = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($object, $position) {
            return (new Hydrator\SpeechAndPosition())->hydrate(
                array_merge($object, ['position' => $position]),
                new Model\SpeechAndPosition()
            );
        }, $speeches, count($speeches) > 0 ? range(0, count($speeches) - 1) : []);
    }

    /**
     * @return \Althingi\Model\SpeechAndPosition[]
     */
    public function fetchByIssue(
        int $assemblyId,
        int $issueId,
        ?KindEnum $kind = KindEnum::A,
        ?int $offset = 0,
        ?int $size = null,
        ?int $words = 1500
    ): array {
        $resultSize = $size !== null ? $size : self::MAX_ROW_COUNT;

        $statement = $this->getDriver()->prepare("
          select *, timestampdiff(SECOND, `from`, `to`) as `time`
          from `Speech`
          where assembly_id = :assembly_id and issue_id = :issue_id and `kind` = :kind
          order by `from`
          limit {$offset}, {$resultSize};
        ");
        $statement->execute(['assembly_id' => $assemblyId, 'issue_id' => $issueId, 'kind' => $kind->value]);

        if ($size) {
            $speeches = $statement->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $wordCount = 0;
            $itemCount = 0;
            $speeches = [];
            do {
                $object = $statement->fetch(PDO::FETCH_ASSOC);
                $wordCount += $object['word_count'];
                $itemCount++;
                if ($object) {
                    $speeches[] = $object;
                }
            } while (($wordCount < $words) && ($itemCount < 25) && $object !== false);
        }
        $statement->closeCursor();

        return array_map(function ($object, $position) {
            return (new Hydrator\SpeechAndPosition())->hydrate(
                array_merge($object, ['position' => $position]),
                new Model\SpeechAndPosition()
            );
        }, $speeches, count($speeches) > 0 ? range($offset, $offset + count($speeches) - 1) : []);
    }

    /**
     * Get a fixed size section of the speech list which will contain
     * the speech with the given ID.
     *
     * Let's say that we want the chunk size to be 25. Further more let's say that in a
     * list of speeches for a given issue in a given assembly, the given speech entry is number 78.
     *
     * This method will return entries from 75 to 100. As 75 is the closest number dividable by 25 (that
     * will contain 78 if 25 is added to it). Further more 100 is the distance from 75 in a chunk size of 25.
     *
     * @return \Althingi\Model\SpeechAndPosition[]
     */
    public function fetch(string $id, int $assemblyId, int $issueId, ?int $size = 25, ?KindEnum $kind = KindEnum::A): array
    {
        $pointer = 0;
        $hasResult = false;
        $statement = $this->getDriver()->prepare(
            'select * from `Speech` s
            where s.`assembly_id` = :assembly_id and s.`issue_id` = :issue_id and s.kind = :kind
            order by s.`from`'
        );
        $statement->execute(['assembly_id' => $assemblyId, ':issue_id' => $issueId, 'kind' => $kind->value]);

        while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
            if ($row->speech_id == $id) {
                $hasResult = true;
                break;
            }
            $pointer++;
        }

        if ($hasResult == false) {
            return [];
        }

        $rangeBegin = ($pointer - ($pointer % $size));

        $statement = $this->getDriver()->prepare(
            'select * from `Speech` s
            where s.`assembly_id` = :assembly_id and s.`issue_id` = :issue_id
            order by s.`from`
            limit ' . $rangeBegin . ', ' . $size
        );
        $statement->execute(['assembly_id' => $assemblyId, ':issue_id' => $issueId]);
        $speeches = $statement->fetchAll(PDO::FETCH_ASSOC);
        $rangeEnd = $rangeBegin + count($speeches);

        return array_map(function ($object, $position) {
            return (new Hydrator\SpeechAndPosition())->hydrate(
                array_merge($object, ['position' => $position]),
                new Model\SpeechAndPosition()
            );
        }, $speeches, range($rangeBegin, $rangeEnd - 1));
    }

    /**
     * This is a Generator
     * @return \Althingi\Model\Speech[] | void
     */
    public function fetchAll()
    {
        $statement = $this->getDriver()->prepare('select * from `Speech`');
        $statement->execute();

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            yield (new Hydrator\Speech())->hydrate($row, new Model\Speech());
        }

        $statement->closeCursor();

        return;
    }

    public function fetchAllGenerator(?int $assemblyId = null, ?int $issueId = null): Generator
    {
        if ($assemblyId !== null && $issueId === null) {
            $statement = $this->getDriver()
                ->prepare('select * from `Speech` where assembly_id = :assembly_id');
            $statement->execute(['assembly_id' => $assemblyId]);
        } elseif ($assemblyId !== null && $issueId !== null) {
            $statement = $this->getDriver()
                ->prepare('select * from `Speech` where assembly_id = :assembly_id and issue_id = :issue_id');
            $statement->execute([
                'assembly_id' => $assemblyId,
                'issue_id' => $issueId,
            ]);
        } else {
            $statement = $this->getDriver()
                ->prepare('select * from `Speech` order by `assembly_id`');
            $statement->execute();
        }

        while (($object = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            yield (new Hydrator\Speech)->hydrate($object, new Model\Speech());
        }
        $statement->closeCursor();
        return null;
    }

    public function countByIssue(int $assemblyId, int $issueId, ?KindEnum $kind = KindEnum::A): int
    {
        $statement = $this->getDriver()->prepare("
          select count(*) from `Speech`
          where assembly_id = :assembly_id and issue_id = :issue_id and kind = :kind
        ");
        $statement->execute(['assembly_id' => $assemblyId, 'issue_id' => $issueId, 'kind' => $kind->value]);
        return $statement->fetchColumn(0);
    }

    /**
     * Will sum up speech time per issue and return the frequency
     * on a month bases.
     *
     * @return \Althingi\Model\DateAndCount[]
     */
    public function fetchFrequencyByIssue(int $assemblyId, int $issueId, ?KindEnum $kind = KindEnum::A): array
    {
        $statement = $this->getDriver()->prepare('
            select date_format(`from`, "%Y-%m-%d 00:00:00") as `date`,
            (sum(time_to_sec(timediff(`to`, `from`)))) as `count`
            from `Speech`
            where assembly_id = :assembly_id and issue_id = :issue_id and kind = :kind
            group by date_format(`from`, "%Y-%m-%d")
            having `count` is not null
            order by `from`;
        ');

        $statement->execute([
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'kind' => $kind->value
        ]);

        return array_map(function ($speech) {
            return (new Hydrator\DateAndCount())->hydrate($speech, new Model\DateAndCount());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Will sum up speech time per assembly and return frequency
     * on a day bases.
     *
     * Return date and time in seconds.
     *
     * @return \Althingi\Model\DateAndCount[]
     */
    public function fetchFrequencyByAssembly(int $assemblyId, ?array $kind = [KindEnum::A]): array
    {
        $categories = count($kind) > 0
            ? 'and `kind` in (' . implode(',', array_map(function (KindEnum $item) {
                return '"' . $item->value . '"';
            }, $kind)) . ')'
            : '';

        $statement = $this->getDriver()->prepare(
            "select date_format(`date`, \"%Y-%m-%d 00:00:00\") as `date`, sum(`diff`) as `count` from (
                select date(`from`) as `date`, time_to_sec(timediff(`to`, `from`)) as `diff`
                from `Speech`
                where assembly_id = :assembly_id and (`from` is not null or `to` is not null) {$categories}
            ) as G group by `date` order by `date`;"
        );
        $statement->execute(['assembly_id' => $assemblyId]);

        return array_map(function ($speech) {
            return (new Hydrator\DateAndCount())->hydrate($speech, new Model\DateAndCount());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function countTotalTimeByAssemblyAndCongressman(
        int $assemblyId,
        int $congressmanId,
        ?array $kind = [KindEnum::A]
    ): int {
        $categories = count($kind) > 0
            ? 'and S.kind in (' . implode(',', array_map(function (KindEnum $item) {
                return '"' . $item->value . '"';
            }, $kind)) . ')'
            : '';

        $statement = $this->getDriver()->prepare("
            select sum(`diff`) from (
                select *, time_to_sec(timediff(S.`to`, S.`from`)) as `diff`
                from `Speech` S
                where S.`assembly_id` = :assembly_id
                  and S.`congressman_id` = :congressman_id
                  {$categories}
            ) as D;
        ");
        $statement->execute([
            'assembly_id' => $assemblyId,
            'congressman_id' => $congressmanId,
        ]);
        return (int) $statement->fetchColumn(0);
    }

    public function create(Model\Speech $data): string
    {
        $data->setWordCount($data->getText() ? str_word_count($data->getText()) : 0);
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('Speech', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new AddEvent(new IndexableSpeechPresenter($data), ['rows' => $statement->rowCount()]),
        );

        return $this->getDriver()->lastInsertId();
    }

    public function save(Model\Speech $data): int
    {
        $data->setWordCount($data->getText() ? str_word_count($data->getText()) : 0);
        $statement = $this->getDriver()->prepare($this->toSaveString('Speech', $data));
        $statement->execute($this->toSqlValues($data));

        switch ($statement->rowCount()) {
            case 1:
                $this->getEventDispatcher()->dispatch(
                    new AddEvent(new IndexableSpeechPresenter($data), ['rows' => $statement->rowCount()]),
                );
                break;
            case 0:
            case 2:
                $this->getEventDispatcher()->dispatch(
                    new UpdateEvent(new IndexableSpeechPresenter($data), ['rows' => $statement->rowCount()]),
                );
                break;
        }
        return $statement->rowCount();
    }

    public function update(Model\Speech $data): int
    {
        $data->setWordCount($data->getText() ? str_word_count($data->getText()) : 0);
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('Speech', $data, "speech_id='{$data->getSpeechId()}'")
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new UpdateEvent(new IndexableSpeechPresenter($data), ['rows' => $statement->rowCount()]),
        );

        return $statement->rowCount();
    }
}
