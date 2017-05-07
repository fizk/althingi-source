<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 8/06/15
 * Time: 9:16 PM
 */

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use PDO;
use Althingi\Model\Congressman as CongressmanModel;
use Althingi\Model\CongressmanAndParty as CongressmanAndPartyModel;
use Althingi\Model\CongressmanAndCabinet as CongressmanAndCabinetModel;
use Althingi\Model\CongressmanAndDateRange as CongressmanAndDateRangeModel;
use Althingi\Model\Proponent as ProponentModel;
use Althingi\Model\President as PresidentModel;
use Althingi\Hydrator\Congressman as CongressmanHydrator;
use Althingi\Hydrator\CongressmanAndParty as CongressmanAndPartyHydrator;
use Althingi\Hydrator\CongressmanAndCabinet as CongressmanAndCabinetHydrator;
use Althingi\Hydrator\CongressmanAndRange as CongressmanAndRangeHydrator;
use Althingi\Hydrator\Proponent as ProponentHydrator;
use Althingi\Hydrator\President as PresidentHydrator;

/**
 * Class Congressman
 * @package Althingi\Service
 */
class Congressman implements DatabaseAwareInterface
{
    const CONGRESSMAN_TYPE_MP = 'parliamentarian';
    const CONGRESSMAN_TYPE_SUBSTITUTE = 'substitute';
    const CONGRESSMAN_TYPE_WITH_SUBSTITUTE = 'with-substitute';

    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * Get one Congressman.
     *
     * @param int $id
     * @return \Althingi\Model\Congressman
     */
    public function get(int $id): ?CongressmanModel
    {
        $statement = $this->getDriver()->prepare("select * from `Congressman` C where congressman_id = :id");
        $statement->execute(['id' => $id]);
        $object =  $statement->fetch(PDO::FETCH_ASSOC);

        return $object
            ? (new CongressmanHydrator())->hydrate($object, new CongressmanModel())
            : null ;
    }

    /**
     * Get all Assemblies.
     *
     * @param int $offset
     * @param int $size
     * @return \Althingi\Model\CongressmanAndParty[]
     */
    public function fetchAll(int $offset, int $size): array
    {
        $statement = $this->getDriver()->prepare("
            select * from `Congressman` C order by C.`name` asc
            limit {$offset}, {$size}
        ");
        $statement->execute();
        return array_map(function ($object) {
            return (new CongressmanHydrator())->hydrate($object, new CongressmanModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param $assemblyId
     * @param null $congressmanType
     * @return \Althingi\Model\CongressmanAndParty[]
     */
    public function fetchByAssembly(int $assemblyId, $congressmanType = null): array
    {
        $statement;
        switch ($congressmanType) {
            case self::CONGRESSMAN_TYPE_MP:
                $statement = $this->getDriver()->prepare(
                    'select C.*, S.party_id from `Session` S
                    join `Congressman` C on (C.congressman_id = S.congressman_id)
                    where S.assembly_id = :assembly_id and S.`type` = \'þingmaður\'
                    group by S.congressman_id order by S.party_id, C.name;'
                );
                break;
            case self::CONGRESSMAN_TYPE_SUBSTITUTE:
                $statement = $this->getDriver()->prepare(
                    'select C.*, S.party_id from `Session` S
                    join `Congressman` C on (C.congressman_id = S.congressman_id)
                    where S.assembly_id = :assembly_id and S.`type` = \'varamaður\'
                    group by S.congressman_id order by S.party_id, C.name;'
                );
                break;
            case self::CONGRESSMAN_TYPE_WITH_SUBSTITUTE:
                //TODO do I need this?
                return [];
                break;
            default:
                $statement = $this->getDriver()->prepare(
                    'select C.*, S.party_id from `Session` S
                    join `Congressman` C on (C.congressman_id = S.congressman_id)
                    where S.assembly_id = :assembly_id
                    group by S.congressman_id order by S.party_id, C.name;'
                );
                break;
        }
        $statement->execute(['assembly_id' => $assemblyId]);
        return array_map(function ($object) {
            return (new CongressmanAndPartyHydrator())->hydrate($object, new CongressmanAndPartyModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param int $cabinetId
     * @return \Althingi\Model\CongressmanAndCabinet[]
     */
    public function fetchByCabinet(int $cabinetId): array
    {
        $statement = $this->getDriver()->prepare(
            'select C.*, CC.`title`, CC.`from` as `date` from `Cabinet_has_Congressman` CC
            join `Congressman` C on (CC.congressman_id = C.`congressman_id`)
            where CC.`cabinet_id` = :cabinet_id order by C.`name`;'
        );
        $statement->execute(['cabinet_id' => $cabinetId]);
        return array_map(function ($object) {
            return (new CongressmanAndCabinetHydrator())->hydrate($object, new CongressmanAndCabinetModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param int $assemblyId
     * @param int $issueId
     * @return \Althingi\Model\CongressmanAndDateRange[]
     */
    public function fetchAccumulatedTimeByIssue(int $assemblyId, int $issueId): array
    {
        $statement = $this->getDriver()->prepare('
            select C.*, (sum(`diff`)) as `time`, date(`from`) as `begin`, null as `end` from (
                select *, timediff(`to`, `from`) as `diff`
                from `Speech` D
                where D.assembly_id = :assembly_id and D.issue_id = :issue_id
            ) S
            join `Congressman` C on (C.congressman_id = S.congressman_id)
            group by S.congressman_id
            order by `time` desc;
        ');
        $statement->execute([
            'issue_id' => $issueId,
            'assembly_id' => $assemblyId
        ]);

        return array_map(function ($object) {
            return (new CongressmanAndRangeHydrator())->hydrate($object, new CongressmanAndDateRangeModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param int $assemblyId
     * @param int $documentId
     * @return \Althingi\Model\Proponent[]
     */
    public function fetchProponents(int $assemblyId, int $documentId): array
    {
        $statement = $this->getDriver()->prepare(
            'select C.*, D.`minister` from `Document_has_Congressman` D
            join `Congressman` C on (C.congressman_id = D.congressman_id)
            where assembly_id = :assembly_id and document_id = :document_id
            order by D.`order` asc;'
        );
        $statement->execute([
            'assembly_id' => $assemblyId,
            'document_id' => $documentId
        ]);

        return array_map(function ($object) {
            return (new ProponentHydrator())->hydrate($object, new ProponentModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }
    /**
     * @param int $assemblyId
     * @param int $issueId
     * @return \Althingi\Model\Proponent[]
     */
    public function fetchProponentsByIssue(int $assemblyId, int $issueId): array
    {
        $statement = $this->getDriver()->prepare(
            'select C.*, A.`minister`, A.`order` from ( 
                select DC.* from `Document_has_Congressman` DC
                    join `Document` D on (
                      D.`document_id` = DC.`document_id` and 
                      D.`issue_id` = DC.`issue_id` and 
                      D.`assembly_id` = DC.`assembly_id`)
                    where DC.`issue_id` = :issue_id and DC.`assembly_id` = :assembly_id
                    order by D.`date`
            ) as A
            join `Congressman` C on (C.`congressman_id` = A.`congressman_id`) order by A.`order`'
        );
        $statement->execute([
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId
        ]);

        return array_map(function ($object) {
            return (new ProponentHydrator())->hydrate($object, new ProponentModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @return \Althingi\Model\President[]
     */
    public function fetchPresidents(): array
    {
        $statement = $this->getDriver()->prepare("
            select * from `President` P
            join `Congressman` C on (C.`congressman_id` = P.`congressman_id`);
        ");
        $statement->execute();

        return array_map(function ($object) {
            return (new PresidentHydrator())->hydrate($object, new PresidentModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param int $assemblyId
     * @return \Althingi\Model\President[]
     */
    public function fetchPresidentsByAssembly(int $assemblyId): array
    {
        $statement = $this->getDriver()->prepare("
            select * from `President` P
            join `Congressman` C on (C.`congressman_id` = P.`congressman_id`)
            where P.`assembly_id` = :assembly_id;
        ");
        $statement->execute(['assembly_id' => $assemblyId]);

        return array_map(function ($object) {
            return (new PresidentHydrator())->hydrate($object, new PresidentModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Create one Congressman. This method accepts object
     * from corresponding Form.
     *
     * @param \Althingi\Model\Congressman $data
     * @return int
     */
    public function create(CongressmanModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('Congressman', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $this->getDriver()->lastInsertId();
    }

    /**
     * Update one Congressman. This method accepts object
     * from corresponding Form.
     *
     * @param \Althingi\Model\Congressman $data
     * @return int Should be 1, for one entry updated.
     */
    public function update(CongressmanModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('Congressman', $data, "congressman_id={$data->getCongressmanId()}")
        );
        $statement->execute($this->toSqlValues($data));

        return $statement->rowCount();
    }

    /**
     * Delete one congressman.
     *
     * @param $id
     * @return int Should be 1, for one entry deleted.
     */
    public function delete(int $id): int
    {
        $statement = $this->getDriver()->prepare("
            delete from `Congressman`
            where congressman_id = :id
        ");
        $statement->execute(['id' => $id]);
        return $statement->rowCount();
    }

    /**
     * Count all Congressmen.
     *
     * @return int
     */
    public function count(): int
    {
        $statement = $this->getDriver()->prepare("
            select count(*) from `Congressman` C
        ");
        $statement->execute();
        return (int) $statement->fetchColumn(0);
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
}
