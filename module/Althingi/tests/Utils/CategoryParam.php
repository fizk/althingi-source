<?php
namespace AlthingiTest\Utils;

use PHPUnit\Framework\TestCase;

class CategoryParamTest extends TestCase
{
    public function testGetCategoriesFromQuery()
    {
        $this->assertEquals((new CategoryParamConcreteClass('a,b'))->getCategoriesFromQuery(), ['A', 'B']);
        $this->assertEquals((new CategoryParamConcreteClass('a,b,c'))->getCategoriesFromQuery(), ['A', 'B']);
        $this->assertEquals((new CategoryParamConcreteClass('a,bc'))->getCategoriesFromQuery(), ['A', 'B']);
        $this->assertEquals((new CategoryParamConcreteClass('b,A'))->getCategoriesFromQuery(), ['B', 'A']);
        $this->assertEquals((new CategoryParamConcreteClass('b , A'))->getCategoriesFromQuery(), ['B', 'A']);
        $this->assertEquals((new CategoryParamConcreteClass('b'))->getCategoriesFromQuery(), ['B']);
        $this->assertEquals((new CategoryParamConcreteClass(''))->getCategoriesFromQuery(), null);
        $this->assertEquals((new CategoryParamConcreteClass(null))->getCategoriesFromQuery(), null);
    }

    public function testGetCategoryFromQuery()
    {
        $this->assertEquals((new CategoryParamConcreteClass('a,b'))->getCategoryFromQuery(), 'A');
        $this->assertEquals((new CategoryParamConcreteClass('a,b,c'))->getCategoryFromQuery(), 'A');
        $this->assertEquals((new CategoryParamConcreteClass('a,bc'))->getCategoryFromQuery(), 'A');
        $this->assertEquals((new CategoryParamConcreteClass('b,A'))->getCategoryFromQuery(), 'B');
        $this->assertEquals((new CategoryParamConcreteClass('b , A'))->getCategoryFromQuery(), 'B');
        $this->assertEquals((new CategoryParamConcreteClass('b'))->getCategoryFromQuery(), 'B');
        $this->assertEquals((new CategoryParamConcreteClass(''))->getCategoryFromQuery(), null);
        $this->assertEquals((new CategoryParamConcreteClass(null))->getCategoryFromQuery(), null);
    }
}
