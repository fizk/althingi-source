<?php

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use Althingi\Presenters\IndexableSpeechPresenter;
use Althingi\ServiceEvents\AddEvent;
use Althingi\ServiceEvents\UpdateEvent;
use PDO;
use Althingi\Hydrator\Speech as SpeechHydrator;
use Althingi\Hydrator\SpeechAndPosition as SpeechAndPositionHydrator;
use Althingi\Hydrator\DateAndCount as DateAndCountHydrator;
use Althingi\Model\Speech as SpeechModel;
use Althingi\Model\SpeechAndPosition as SpeechAndPositionModel;
use Althingi\Model\DateAndCount as DateAndCountModel;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareInterface;

/**
 * Class Speech
 * @package Althingi\Service
 */
class Speech implements DatabaseAwareInterface, EventManagerAwareInterface
{
    use DatabaseService;

    const MAX_ROW_COUNT = '18446744073709551615';

    /**
     * @var \PDO
     */
    private $pdo;

    /** @var  \Zend\EventManager\EventManager */
    private $eventManager;

    /**
     * Get one speech item.
     *
     * @param string $id
     * @return \Althingi\Model\Speech
     */
    public function get(string $id): ?SpeechModel
    {
        $statement = $this->getDriver()->prepare(
            'select * from `Speech` where speech_id = :speech_id'
        );
        $statement->execute(['speech_id' => $id]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new SpeechHydrator())->hydrate($object, new SpeechModel())
            : null ;
    }

    /**
     * @return SpeechModel|null
     */
    public function getLastActive(): ?SpeechModel
    {
        $statement = $this->getDriver()->prepare(
            'select * from `Speech` where `text` is not null order by `from` desc;'
        );
        $statement->execute([]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new SpeechHydrator())->hydrate($object, new SpeechModel())
            : null ;
    }

    /**
     * This makes two queries, one that for a single congressman will count time
     * for each type of speech. The second one will count fime for congressman type.
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
     * @param $assemblyId
     * @param $congressmanId
     * @return object
     */
    public function getFrequencyByAssemblyAndCongressman(int $assemblyId, int $congressmanId)
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
            'type' =>  array_map(function ($speech) {
                $speech->total = (int) $speech->total;
                return $speech;
            }, $speechTypeStatement->fetchAll()),
            'congressman_type' =>  array_map(function ($speech) {
                $speech->congressman_type = $speech->congressman_type
                    ? $speech->congressman_type
                    : 'þingmaður';
                $speech->total = (int) $speech->total;
                return $speech;
            }, $congressmanTypeStatement->fetchAll()),
        ];
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
     * @param string $id
     * @param int $assemblyId
     * @param int $issueId
     * @param int $size
     * @return \Althingi\Model\SpeechAndPosition[]
     */
    public function fetch(string $id, int $assemblyId, int $issueId, int $size = 25): array
    {
        $pointer = 0;
        $hasResult = false;
        $statement = $this->getDriver()->prepare(
            'select * from `Speech` s 
            where s.`assembly_id` = :assembly_id and s.`issue_id` = :issue_id
            order by s.`from`'
        );
        $statement->execute(['assembly_id' => $assemblyId, ':issue_id' => $issueId]);

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
            return (new SpeechAndPositionHydrator())->hydrate(
                array_merge($object, ['position' => $position]),
                new SpeechAndPositionModel()
            );
        }, $speeches, range($rangeBegin, $rangeEnd - 1));
    }

    /**
     * This is a Generator
     * @return \Althingi\Model\Speech[]
     */
    public function fetchAll()
    {
        $statement = $this->getDriver()->prepare('select * from `Speech`');
        $statement->execute();

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            yield (new SpeechHydrator())->hydrate($row, new SpeechModel());
        }

        $statement->closeCursor();

        return;
    }

    /**
     * Fetch all speeches by issue.
     *
     * @param int $assemblyId
     * @param int $issueId
     * @param int $offset
     * @param int $size
     * @param int $words
     * @return \Althingi\Model\SpeechAndPosition[]
     */
    public function fetchByIssue(
        int $assemblyId,
        int $issueId,
        int $offset = 0,
        int $size = null,
        int $words = 1500
    ): array {
        $resultSize = $size !== null ? $size : self::MAX_ROW_COUNT;

        $statement = $this->getDriver()->prepare("
          select *, timestampdiff(SECOND, `from`, `to`) as `time`
          from `Speech`
          where assembly_id = :assembly_id and issue_id = :issue_id
          order by `from`
          limit {$offset}, {$resultSize};
        ");
        $statement->execute(['assembly_id' => $assemblyId, 'issue_id' => $issueId]);

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
            return (new SpeechAndPositionHydrator())->hydrate(
                array_merge($object, ['position' => $position]),
                new SpeechAndPositionModel()
            );
        }, $speeches, count($speeches) > 0 ? range($offset, $offset + count($speeches) - 1) : []);
    }

    /**
     * Count all speeches by issue.
     *
     * @param int $assemblyId
     * @param int $issueId
     * @return int
     */
    public function countByIssue(int $assemblyId, int $issueId): int
    {
        $statement = $this->getDriver()->prepare("
          select count(*) from `Speech`
          where assembly_id = :assembly_id and issue_id = :issue_id
        ");
        $statement->execute(['assembly_id' => $assemblyId, 'issue_id' => $issueId]);
        return $statement->fetchColumn(0);
    }

    /**
     * Will sum up speech time per issue and return the frequency
     * on a month bases.
     *
     * @param $assemblyId
     * @param $issueId
     * @return \Althingi\Model\DateAndCount[]
     */
    public function fetchFrequencyByIssue(int $assemblyId, int $issueId): array
    {
        $statement = $this->getDriver()->prepare('
            select date_format(`from`, "%Y-%m-%d 00:00:00") as `date`, 
            (sum(time_to_sec(timediff(`to`, `from`)))) as `count`
            from `Speech`
            where assembly_id = :assembly_id and issue_id = :issue_id
            group by date_format(`from`, "%Y-%m-%d")
            having `count` is not null
            order by `from`;
        ');

        $statement->execute([
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId
        ]);

        return array_map(function ($speech) {
            return (new DateAndCountHydrator())->hydrate($speech, new DateAndCountModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Will sum up speech time per assembly and return frequency
     * on a day bases.
     *
     * Return date and time in seconds.
     *
     * @param int $assemblyId
     * @return \Althingi\Model\DateAndCount[]
     */
    public function fetchFrequencyByAssembly(int $assemblyId): array
    {
        $statement = $this->getDriver()->prepare(
            'select date_format(`date`, "%Y-%m-%d 00:00:00") as `date`, sum(`diff`) as `count` from (
                select date(`from`) as `date`, time_to_sec(timediff(`to`, `from`)) as `diff`
                from `Speech`
                where assembly_id = :assembly_id and (`from` is not null or `to` is not null)
            ) as G group by `date` order by `date`;'
        );
        $statement->execute(['assembly_id' => $assemblyId]);

        return array_map(function ($speech) {
            return (new DateAndCountHydrator())->hydrate($speech, new DateAndCountModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param int $assemblyId
     * @param int $congressmanId
     * @return int
     */
    public function countTotalTimeByAssemblyAndCongressman(int $assemblyId, int $congressmanId): int
    {
        $statement = $this->getDriver()->prepare('
            select sum(`diff`) from (
                select *, time_to_sec(timediff(S.`to`, S.`from`)) as `diff` 
                from `Speech` S where S.`assembly_id` = :assembly_id and S.`congressman_id` = :congressman_id
            ) as D;
        ');
        $statement->execute([
            'assembly_id' => $assemblyId,
            'congressman_id' => $congressmanId,
        ]);
        return (int) $statement->fetchColumn(0);
    }

    /**
     * Create one Speech. Accepts object from
     * corresponding Form.
     *
     * @param \Althingi\Model\Speech $data
     * @return int
     */
    public function create(SpeechModel $data)
    {
        $data->setWordCount(str_word_count($data->getText()));
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('Speech', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventManager()->trigger(new AddEvent(new IndexableSpeechPresenter($data)));

        return $this->getDriver()->lastInsertId();
    }

    /**
     * @param \Althingi\Model\Speech $data
     * @return int
     */
    public function save(SpeechModel $data)
    {
        $data->setWordCount(str_word_count($data->getText()));
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('Speech', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventManager()->trigger(new AddEvent(new IndexableSpeechPresenter($data)));

        return $statement->rowCount();
    }

    /**
     * Update one entry.
     *
     * @param \Althingi\Model\Speech $data
     * @return int
     */
    public function update(SpeechModel $data)
    {
        $data->setWordCount(str_word_count($data->getText()));
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('Speech', $data, "speech_id='{$data->getSpeechId()}'")
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventManager()->trigger(new UpdateEvent(new IndexableSpeechPresenter($data)));

        return $statement->rowCount();
    }

    /**
     * @param \PDO $pdo
     */
    public function setDriver(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return \PDO
     */
    public function getDriver()
    {
        return $this->pdo;
    }

    /**
     * Inject an EventManager instance
     *
     * @param  EventManagerInterface $eventManager
     * @return void
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * Retrieve the event manager
     *
     * Lazy-loads an EventManager instance if none registered.
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }
}
