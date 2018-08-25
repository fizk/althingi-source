<?php
namespace Althingi\Utils;

use PHPUnit_Framework_TestCase;

class CategoryParamTest extends PHPUnit_Framework_TestCase
{
    public function testGetCategoriesFromQuery()
    {
        $this->assertEquals((new TestClass('a,b'))->getCategoriesFromQuery(), ['A', 'B']);
        $this->assertEquals((new TestClass('a,b,c'))->getCategoriesFromQuery(), ['A', 'B']);
        $this->assertEquals((new TestClass('a,bc'))->getCategoriesFromQuery(), ['A', 'B']);
        $this->assertEquals((new TestClass('b,A'))->getCategoriesFromQuery(), ['B', 'A']);
        $this->assertEquals((new TestClass('b , A'))->getCategoriesFromQuery(), ['B', 'A']);
        $this->assertEquals((new TestClass('b'))->getCategoriesFromQuery(), ['B']);
        $this->assertEquals((new TestClass(''))->getCategoriesFromQuery(), null);
        $this->assertEquals((new TestClass(null))->getCategoriesFromQuery(), null);
    }

    public function testGetCategoryFromQuery()
    {
        $this->assertEquals((new TestClass('a,b'))->getCategoryFromQuery(), 'A');
        $this->assertEquals((new TestClass('a,b,c'))->getCategoryFromQuery(), 'A');
        $this->assertEquals((new TestClass('a,bc'))->getCategoryFromQuery(), 'A');
        $this->assertEquals((new TestClass('b,A'))->getCategoryFromQuery(), 'B');
        $this->assertEquals((new TestClass('b , A'))->getCategoryFromQuery(), 'B');
        $this->assertEquals((new TestClass('b'))->getCategoryFromQuery(), 'B');
        $this->assertEquals((new TestClass(''))->getCategoryFromQuery(), null);
        $this->assertEquals((new TestClass(null))->getCategoryFromQuery(), null);
    }
}

class TestClass
{
    use CategoryParam;

    private $param;

    public function __construct($param)
    {
        $this->param = $param;
    }

    public function params()
    {
        return $this;
    }

    public function fromQuery($name, $default)
    {
        return $this->param;
    }
}
