<?php
/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 11/27/16
 * Time: 7:37 PM
 */

namespace Althingi\Service;

use Althingi\Model\ModelInterface;

class DatabaseServiceTest extends \PHPUnit_Framework_TestCase
{
    private $class;

    private $data;

    public function setUp()
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
            public function toArray()
            {
                return [
                    'hundur' => 'voff'
                ];
            }

            public function jsonSerialize()
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
