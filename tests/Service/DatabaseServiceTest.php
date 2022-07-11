<?php

namespace Althingi\Service;

use Althingi\Model\ModelInterface;
use Althingi\Service\DatabaseService;
use PHPUnit\Framework\TestCase;

class DatabaseServiceTest extends TestCase
{
    private $class;

    private $data;

    public function setUp(): void
    {
        $this->class = new class {
            use DatabaseService;

            public function testInsert($table, $data)
            {
                return $this->toInsertString($table, $data);
            }

            public function testUpdate($table, $data, $condition)
            {
                return $this->toUpdateString($table, $data, $condition);
            }
        };

        $this->data = new class implements ModelInterface
        {
            public function toArray(): array
            {
                return [
                    'hundur' => 'voff'
                ];
            }

            public function jsonSerialize(): array
            {
                return $this->toArray();
            }
        };
    }

    public function testInsert()
    {
        $insertString = $this->class->testInsert('Table', $this->data);
        $this->assertEquals('INSERT INTO `Table` ( `hundur`) VALUES ( :hundur);', $insertString);
    }

    public function testUpdate()
    {
        $updateString = $this->class->testUpdate('Table', $this->data, 'id=3');
        $this->assertEquals('UPDATE `Table` SET  `hundur` = :hundur WHERE id=3;', $updateString);
    }
}
