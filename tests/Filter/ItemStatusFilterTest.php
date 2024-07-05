<?php

namespace Althingi\Filter;

use Althingi\Filter\ItemStatusFilter;
use PHPUnit\Framework\Attributes\{DataProvider, Test};
use PHPUnit\Framework\TestCase;

class ItemStatusFilterTest extends TestCase
{
    public static function filterStringProvider()
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

            ['Bíður 1. umræðu',                                                   'Bíður 1. umræðu'],
            ['Bíður 2. umræðu',                                                   'Bíður 2. umræðu'],
            ['Bíður 3. umræðu',                                                   'Bíður 3. umræðu'],
            ['Bíður fyrri umræðu',                                                'Bíður 1. umræðu'],
            ['Bíður síðari umræðu',                                               'Bíður 2. umræðu'],
            ['Ekki samþykkt',                                                     'Ekki samþykkt'],
            ['Ekki útrætt á 115. þingi. (Beið fyrri umræðu.)',                    'Bíður 1. umræðu'],
            ['Ekki útrætt á 115. þingi. (Beið síðari umræðu.)',                   'Bíður 2. umræðu'],
            ['Ekki útrætt á 115. þingi. (Var í nefnd eftir fyrri umræðu.)',       'Í nefnd eftir 1. umræðu'],
            ['Ekki útrætt á 116. þingi. (Beið fyrri umræðu.)',                    'Bíður 1. umræðu'],
            ['Ekki útrætt á 116. þingi. (Beið síðari umræðu.)',                   'Bíður 2. umræðu'],
            ['Ekki útrætt á 116. þingi. (Var í nefnd eftir fyrri umræðu.)',       'Í nefnd eftir 1. umræðu'],
            ['Ekki útrætt á 117. þingi. (Beið fyrri umræðu.)',                    'Bíður 1. umræðu'],
            ['Ekki útrætt á 117. þingi. (Beið síðari umræðu.)',                   'Bíður 2. umræðu'],
            ['Ekki útrætt á 117. þingi. (Var í nefnd eftir fyrri umræðu.)',       'Í nefnd eftir 1. umræðu'],
            ['Ekki útrætt á 118. þingi. (Var í nefnd eftir fyrri umræðu.)',       'Í nefnd eftir 1. umræðu'],
            ['Ekki útrætt á 119. þingi. (Beið fyrri umræðu.)',                    'Bíður 1. umræðu'],
            ['Ekki útrætt á 119. þingi. (Var í nefnd eftir fyrri umræðu.)',       'Í nefnd eftir 1. umræðu'],
            ['Fyrirspurnin var felld niður vegna ráðherraskipta','Fyrirspurnin var felld niður vegna ráðherraskipta'],
            ['Fyrirspurnin var kölluð aftur',                                     'Fyrirspurnin var kölluð aftur'],
            ['Fyrirspurninni hefur ekki verið svarað',                    'Fyrirspurninni hefur ekki verið svarað'],
            ['Fyrirspurninni var ekki svarað',                            'Fyrirspurninni var ekki svarað'],
            ['Fyrirspurninni var svarað munnlega',                        'Fyrirspurninni var svarað munnlega'],
            ['Fyrirspurninni var svarað skriflega',                       'Fyrirspurninni var svarað skriflega'],
            ['Í nefnd eftir 1. umræðu',                                   'Í nefnd eftir 1. umræðu'],
            ['Í nefnd eftir 2. umræðu',                                   'Í nefnd eftir 2. umræðu'],
            ['Í nefnd eftir fyrri umræðu',                                'Í nefnd eftir 1. umræðu'],
            ['Samþykkt sem ályktun Alþingis',                             'Samþykkt sem ályktun Alþingis'],
            ['Samþykkt sem lög frá Alþingi',                              'Samþykkt sem lög frá Alþingi'],
            ['Vísað til ríkisstjórnar',                                   'Vísað til ríkisstjórnar'],
        ];
    }

    #[DataProvider('filterStringProvider')]
    #[Test]
    public function filterTest($provided, $expected)
    {
        $this->assertEquals($expected, (new ItemStatusFilter())->filter($provided));
    }
}
