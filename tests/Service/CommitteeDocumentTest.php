<?php

namespace Althingi\Service;

use Althingi\DatabaseConnectionTrait;
use Althingi\Events\{UpdateEvent, AddEvent};
use Althingi\Model;
use Althingi\Service;
use PHPUnit\Framework\Attributes\{Test};
use PHPUnit\Framework\TestCase;

class CommitteeDocumentTest extends TestCase
{
    use DatabaseConnectionTrait;

    #[Test]
    public function getIdentifierSuccessfully()
    {
        $committeeDocument = new Service\CommitteeDocument();
        $committeeDocument->setDriver($this->getPDO());

        $expectedData = 100001;

        $actualData = $committeeDocument->getIdentifier(1,1,1,Model\KindEnum::A,1,'part1');

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function getIdentifierIsNullFailure()
    {
        $committeeDocument = new Service\CommitteeDocument();
        $committeeDocument->setDriver($this->getPDO());

        $actualData = $committeeDocument->getIdentifier(
            12,
            12,
            12,
            Model\KindEnum::A,1,
            'part1'
        );

        $this->assertNull($actualData);
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 2, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 3, 'from' => '2000-01-01', 'to' => null],
            ],
            'Issue' => [
                [
                    'issue_id' => 1,
                    'assembly_id' => 1,
                    'kind' => Model\KindEnum::A->value,
                    'congressman_id' => null,
                    'type' => 'l',
                    'status' => 'some',
                    'type_subname' => 'something'
                ],
            ],
            'Document' => [
                [
                    'document_id' => 1,
                    'issue_id' => 1,
                    'kind' => Model\KindEnum::A->value,
                    'assembly_id' => 1,
                    'date' => '2000-01-01',
                    'url' => '',
                    'type' => 'stjórnarfrumvarp'
                ], [
                    'document_id' => 2,
                    'issue_id' => 1,
                    'kind' => Model\KindEnum::A->value,
                    'assembly_id' => 1,
                    'date' => '2000-01-02',
                    'url' => '',
                    'type' => ''
                ], [
                    'document_id' => 3,
                    'issue_id' => 1,
                    'kind' => Model\KindEnum::A->value,
                    'assembly_id' => 1,
                    'date' => '2000-01-03',
                    'url' => '',
                    'type' => ''
                ],
            ],

            'Committee' => [
                [
                    'committee_id' => 1,
                    'name' => 'name 1',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 2,
                    'abbr_long' => 'abbr_long',
                    'abbr_short' => 'abbr_short'
                ],
                [
                    'committee_id' => 3,
                    'name' => 'name 3',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 2,
                    'abbr_long' => 'abbr_long',
                    'abbr_short' => 'abbr_short'
                ],
            ],
            'Document_has_Committee' => [
                [
                    'document_committee_id' => 200002,
                    'document_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 1,
                    'kind' => Model\KindEnum::A->value,
                    'committee_id' => 1,
                    'part' => 'part2',
                    'name' => null,
                ],
            ],
            'Document_has_Committee' => [
                [
                    'document_committee_id' => 100001,
                    'document_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 1,
                    'kind' => Model\KindEnum::A->value,
                    'committee_id' => 1,
                    'part' => 'part1',
                    'name' => null,
                ],
            ],
        ]);
    }
}
