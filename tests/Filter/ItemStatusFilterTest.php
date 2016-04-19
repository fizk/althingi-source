<?php

/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 19/04/2016
 * Time: 4:19 PM
 */

namespace Althingi\Filter;

use PHPUnit_Framework_TestCase;

class ItemStatusFilterTest extends PHPUnit_Framework_TestCase
{

    public function filterStringProvider()
    {
        return [
            ['', null],
            [null, null],

            ['Ekki útrætt á 130. þingi. (Beið 1. umræðu.)',             'Bíður 1. umræðu'],
            ['Ekki útrætt á 130. þingi. Beið 1. umræðu.',               'Bíður 1. umræðu'],
            ['Ekki útrætt á 130. þingi. Bíður 1. umræðu',               'Bíður 1. umræðu'],
            ['Bíður 1. umræðu.',                                        'Bíður 1. umræðu'],
            ['Ekki útrætt á 131. þingi. Bíður 1. umræðu',               'Bíður 1. umræðu'],
            ['Ekki útrætt á 130. þingi. Bíður 2. umræðu',               'Bíður 2. umræðu'],
            ['Bíður 3. umræðu.',                                        'Bíður 3. umræðu'],
            ['Ekki útrætt á 131. þingi. Bíður 4. umræðu',               'Bíður 4. umræðu'],

            ['Í nefnd eftir 1. umræðu',                                 'Í nefnd eftir 1. umræðu'],
            ['Í nefnd eftir 2. umræðu',                                 'Í nefnd eftir 2. umræðu'],
            ['Ekki útrætt á 135. þingi. (Var í nefnd eftir 1. umræðu.)','Í nefnd eftir 1. umræðu'],
            ['Ekki útrætt á 135. þingi. Var í nefnd eftir 1. umræðu.',  'Í nefnd eftir 1. umræðu'],
            ['Ekki útrætt á 100. þingi. Var í nefnd eftir 2. umræðu.',  'Í nefnd eftir 2. umræðu'],
            ['Í nefnd eftir 1. umræðu.',                                'Í nefnd eftir 1. umræðu'],

            ['Samþykkt sem lög frá Alþingi.',                           'Samþykkt sem lög frá Alþingi'],
            ['Vísað til ríkisstjórnar.',                                'Vísað til ríkisstjórnar'],
            ['Hundur',                                                  'Hundur'],
        ];
    }

    /**
     * @dataProvider filterStringProvider
     * @param string $provided
     * @param string $expected
     */
    public function testFilter($provided, $expected)
    {
        $this->assertEquals($expected, (new ItemStatusFilter())->filter($provided));
    }
}
