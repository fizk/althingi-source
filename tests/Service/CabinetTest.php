<?php

namespace Althingi\Service;

use Althingi\Service\Cabinet;
use Althingi\DatabaseConnection;
use PHPUnit\Framework\TestCase;
use Althingi\Model\Cabinet as CabinetModel;
use PDO;

class CabinetTest extends TestCase
{
    use DatabaseConnection;

    private PDO $pdo;

    public function additionProvider()
    {
        return [
            [135, [['cabinet_id' => 20070524, 'title' => 'title', 'from' => '2007-05-24', 'to' => '2009-02-01']]],
            [136, [['cabinet_id' => 20090201, 'title' => 'title', 'from' => '2009-02-01', 'to' => '2009-05-10']]],

            [137, [['cabinet_id' => 20090510, 'title' => 'title', 'from' => '2009-05-10', 'to' => '2013-05-22']]],
            [138, [['cabinet_id' => 20090510, 'title' => 'title', 'from' => '2009-05-10', 'to' => '2013-05-22']]],
            [139, [['cabinet_id' => 20090510, 'title' => 'title', 'from' => '2009-05-10', 'to' => '2013-05-22']]],
            [140, [['cabinet_id' => 20090510, 'title' => 'title', 'from' => '2009-05-10', 'to' => '2013-05-22']]],
            [141, [['cabinet_id' => 20090510, 'title' => 'title', 'from' => '2009-05-10', 'to' => '2013-05-22']]],

            [142, [['cabinet_id' => 20130523, 'title' => 'title', 'from' => '2013-05-23', 'to' => '2016-04-07']]],
            [143, [['cabinet_id' => 20130523, 'title' => 'title', 'from' => '2013-05-23', 'to' => '2016-04-07']]],
            [144, [['cabinet_id' => 20130523, 'title' => 'title', 'from' => '2013-05-23', 'to' => '2016-04-07']]],
            [145, [['cabinet_id' => 20160407, 'title' => 'title', 'from' => '2016-04-07', 'to' => '2017-01-11']]],

            [146, [['cabinet_id' => 20170111, 'title' => 'title', 'from' => '2017-01-11', 'to' => '2017-11-30']]],
            [147, [['cabinet_id' => 20170111, 'title' => 'title', 'from' => '2017-01-11', 'to' => '2017-11-30']]],

            [148, [['cabinet_id' => 20171130, 'title' => 'title', 'from' => '2017-11-30', 'to' => null]]],
            [149, [['cabinet_id' => 20171130, 'title' => 'title', 'from' => '2017-11-30', 'to' => null]]],
        ];
    }

    /**
     * @dataProvider additionProvider
     * @param int $assembly
     * @param array $cabinets
     */
    public function testFetchByAssembly(int $assembly, array $cabinets)
    {
        $assemblyService = new Cabinet();
        $assemblyService->setDriver($this->pdo);

        $expectedData = array_map(function ($cabinet) {
            return (new \Althingi\Hydrator\Cabinet())->hydrate($cabinet, new CabinetModel);
        }, $cabinets);

        $actualData = $assemblyService->fetchByAssembly($assembly);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchByAssemblyNoResult()
    {
        $assemblyService = new Cabinet();
        $assemblyService->setDriver($this->pdo);

        $expectedData = [];

        $actualData = $assemblyService->fetchByAssembly(40);

        $this->assertEquals($expectedData, $actualData);
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 75, 'from' => '1955-10-08', 'to' => '1956-03-28'],
                ['assembly_id' => 76, 'from' => '1956-10-10', 'to' => '1957-05-31'],
                ['assembly_id' => 77, 'from' => '1957-10-10', 'to' => '1958-06-04'],
                ['assembly_id' => 78, 'from' => '1958-10-10', 'to' => '1959-05-14'],
                ['assembly_id' => 79, 'from' => '1959-07-21', 'to' => '1959-08-15'],
                ['assembly_id' => 80, 'from' => '1959-11-20', 'to' => '1960-06-03'],
                ['assembly_id' => 81, 'from' => '1960-10-10', 'to' => '1961-03-29'],
                ['assembly_id' => 82, 'from' => '1961-10-10', 'to' => '1962-04-18'],
                ['assembly_id' => 83, 'from' => '1962-10-10', 'to' => '1963-04-20'],
                ['assembly_id' => 84, 'from' => '1963-10-10', 'to' => '1964-05-14'],
                ['assembly_id' => 85, 'from' => '1964-10-10', 'to' => '1965-05-12'],
                ['assembly_id' => 86, 'from' => '1965-10-08', 'to' => '1966-05-05'],
                ['assembly_id' => 87, 'from' => '1966-10-10', 'to' => '1967-04-19'],
                ['assembly_id' => 88, 'from' => '1967-10-10', 'to' => '1968-04-20'],
                ['assembly_id' => 89, 'from' => '1968-10-10', 'to' => '1969-05-17'],
                ['assembly_id' => 90, 'from' => '1969-10-10', 'to' => '1970-05-04'],
                ['assembly_id' => 91, 'from' => '1970-10-10', 'to' => '1971-04-07'],
                ['assembly_id' => 92, 'from' => '1971-10-11', 'to' => '1972-05-20'],
                ['assembly_id' => 93, 'from' => '1972-10-10', 'to' => '1973-04-18'],
                ['assembly_id' => 94, 'from' => '1973-10-10', 'to' => '1974-05-09'],
                ['assembly_id' => 95, 'from' => '1974-07-18', 'to' => '1974-09-05'],
                ['assembly_id' => 96, 'from' => '1974-10-29', 'to' => '1975-05-16'],
                ['assembly_id' => 97, 'from' => '1975-10-10', 'to' => '1976-05-19'],
                ['assembly_id' => 98, 'from' => '1976-10-11', 'to' => '1977-05-04'],
                ['assembly_id' => 99, 'from' => '1977-10-10', 'to' => '1978-05-06'],
                ['assembly_id' => 100, 'from' => '1978-10-10', 'to' => '1979-05-23'],
                ['assembly_id' => 101, 'from' => '1979-10-10', 'to' => '1979-10-16'],
                ['assembly_id' => 102, 'from' => '1979-12-12', 'to' => '1980-05-29'],
                ['assembly_id' => 103, 'from' => '1980-10-10', 'to' => '1981-05-25'],
                ['assembly_id' => 104, 'from' => '1981-10-10', 'to' => '1982-05-07'],
                ['assembly_id' => 105, 'from' => '1982-10-11', 'to' => '1983-03-14'],
                ['assembly_id' => 106, 'from' => '1983-10-10', 'to' => '1984-05-22'],
                ['assembly_id' => 107, 'from' => '1984-10-10', 'to' => '1985-06-21'],
                ['assembly_id' => 108, 'from' => '1985-10-10', 'to' => '1986-04-23'],
                ['assembly_id' => 109, 'from' => '1986-10-10', 'to' => '1987-03-19'],
                ['assembly_id' => 110, 'from' => '1987-10-10', 'to' => '1988-05-11'],
                ['assembly_id' => 111, 'from' => '1988-10-10', 'to' => '1989-05-20'],
                ['assembly_id' => 112, 'from' => '1989-10-10', 'to' => '1990-05-15'],
                ['assembly_id' => 113, 'from' => '1990-10-10', 'to' => '1991-03-20'],
                ['assembly_id' => 114, 'from' => '1991-05-13', 'to' => '1991-10-01'],
                ['assembly_id' => 115, 'from' => '1991-10-01', 'to' => '1992-08-17'],
                ['assembly_id' => 116, 'from' => '1992-08-17', 'to' => '1993-10-01'],
                ['assembly_id' => 117, 'from' => '1993-10-01', 'to' => '1994-10-01'],
                ['assembly_id' => 118, 'from' => '1994-10-01', 'to' => '1995-05-16'],
                ['assembly_id' => 119, 'from' => '1995-05-16', 'to' => '1995-10-02'],
                ['assembly_id' => 120, 'from' => '1995-10-02', 'to' => '1996-10-01'],
                ['assembly_id' => 121, 'from' => '1996-10-01', 'to' => '1997-10-01'],
                ['assembly_id' => 122, 'from' => '1997-10-01', 'to' => '1998-10-01'],
                ['assembly_id' => 123, 'from' => '1998-10-01', 'to' => '1999-06-08'],
                ['assembly_id' => 124, 'from' => '1999-06-08', 'to' => '1999-10-01'],
                ['assembly_id' => 125, 'from' => '1999-10-01', 'to' => '2000-10-02'],
                ['assembly_id' => 126, 'from' => '2000-10-02', 'to' => '2001-10-01'],
                ['assembly_id' => 127, 'from' => '2001-10-01', 'to' => '2002-10-01'],
                ['assembly_id' => 128, 'from' => '2002-10-01', 'to' => '2003-05-26'],
                ['assembly_id' => 129, 'from' => '2003-05-26', 'to' => '2003-10-01'],
                ['assembly_id' => 130, 'from' => '2003-10-01', 'to' => '2004-12-31'],
                ['assembly_id' => 131, 'from' => '2004-10-01', 'to' => '2005-09-30'],
                ['assembly_id' => 132, 'from' => '2005-09-30', 'to' => '2006-09-29'],
                ['assembly_id' => 133, 'from' => '2006-09-29', 'to' => '2007-08-01'],
                ['assembly_id' => 134, 'from' => '2007-05-31', 'to' => '2007-09-28'],
                ['assembly_id' => 135, 'from' => '2007-09-28', 'to' => '2008-09-30'],
                ['assembly_id' => 136, 'from' => '2008-09-30', 'to' => '2009-04-25'],
                ['assembly_id' => 137, 'from' => '2009-04-25', 'to' => '2009-09-30'],
                ['assembly_id' => 138, 'from' => '2009-10-01', 'to' => '2010-09-30'],
                ['assembly_id' => 139, 'from' => '2010-10-01', 'to' => '2011-09-30'],
                ['assembly_id' => 140, 'from' => '2011-10-01', 'to' => '2012-09-10'],
                ['assembly_id' => 141, 'from' => '2012-09-11', 'to' => '2013-04-26'],
                ['assembly_id' => 142, 'from' => '2013-06-06', 'to' => '2013-09-30'],
                ['assembly_id' => 143, 'from' => '2013-09-30', 'to' => '2014-09-08'],
                ['assembly_id' => 144, 'from' => '2014-09-09', 'to' => '2015-09-07'],
                ['assembly_id' => 145, 'from' => '2015-09-08', 'to' => '2016-10-28'],
                ['assembly_id' => 146, 'from' => '2016-10-29', 'to' => '2017-09-11'],
                ['assembly_id' => 147, 'from' => '2017-09-12', 'to' => '2017-10-27'],
                ['assembly_id' => 148, 'from' => '2017-10-28', 'to' => '2018-09-10'],
                ['assembly_id' => 149, 'from' => '2018-09-11', 'to' => null],
            ],
            'Cabinet' => [
                ['cabinet_id' => 19040201, 'title' => 'title', 'from' => '1904-02-01', 'to' => '1917-01-04'],
                ['cabinet_id' => 19170104, 'title' => 'title', 'from' => '1917-01-04', 'to' => '1920-02-25'],
                ['cabinet_id' => 19200225, 'title' => 'title', 'from' => '1920-02-25', 'to' => '1922-03-07'],
                ['cabinet_id' => 19220307, 'title' => 'title', 'from' => '1922-03-07', 'to' => '1924-03-22'],
                ['cabinet_id' => 19240322, 'title' => 'title', 'from' => '1924-03-22', 'to' => '1926-07-08'],
                ['cabinet_id' => 19260708, 'title' => 'title', 'from' => '1926-07-08', 'to' => '1927-08-28'],
                ['cabinet_id' => 19270828, 'title' => 'title', 'from' => '1927-08-28', 'to' => '1932-06-03'],
                ['cabinet_id' => 19320603, 'title' => 'title', 'from' => '1932-06-03', 'to' => '1934-07-28'],
                ['cabinet_id' => 19340728, 'title' => 'title', 'from' => '1934-07-28', 'to' => '1938-04-02'],
                ['cabinet_id' => 19380402, 'title' => 'title', 'from' => '1938-04-02', 'to' => '1939-04-17'],
                ['cabinet_id' => 19390417, 'title' => 'title', 'from' => '1939-04-17', 'to' => '1941-11-18'],
                ['cabinet_id' => 19411118, 'title' => 'title', 'from' => '1941-11-18', 'to' => '1942-05-16'],
                ['cabinet_id' => 19420516, 'title' => 'title', 'from' => '1942-05-16', 'to' => '1942-12-16'],
                ['cabinet_id' => 19421216, 'title' => 'title', 'from' => '1942-12-16', 'to' => '1944-10-21'],
                ['cabinet_id' => 19441021, 'title' => 'title', 'from' => '1944-10-21', 'to' => '1947-02-04'],
                ['cabinet_id' => 19470204, 'title' => 'title', 'from' => '1947-02-04', 'to' => '1949-12-06'],
                ['cabinet_id' => 19491206, 'title' => 'title', 'from' => '1949-12-06', 'to' => '1950-03-14'],
                ['cabinet_id' => 19500314, 'title' => 'title', 'from' => '1950-03-14', 'to' => '1953-09-11'],
                ['cabinet_id' => 19530911, 'title' => 'title', 'from' => '1953-09-11', 'to' => '1956-07-24'],
                ['cabinet_id' => 19560724, 'title' => 'title', 'from' => '1956-07-24', 'to' => '1958-12-23'],
                ['cabinet_id' => 19581223, 'title' => 'title', 'from' => '1958-12-23', 'to' => '1959-11-20'],
                ['cabinet_id' => 19591120, 'title' => 'title', 'from' => '1959-11-20', 'to' => '1963-11-14'],
                ['cabinet_id' => 19631114, 'title' => 'title', 'from' => '1963-11-14', 'to' => '1970-07-10'],
                ['cabinet_id' => 19700710, 'title' => 'title', 'from' => '1970-07-10', 'to' => '1971-07-14'],
                ['cabinet_id' => 19710714, 'title' => 'title', 'from' => '1971-07-14', 'to' => '1974-08-28'],
                ['cabinet_id' => 19740828, 'title' => 'title', 'from' => '1974-08-28', 'to' => '1978-09-01'],
                ['cabinet_id' => 19780901, 'title' => 'title', 'from' => '1978-09-01', 'to' => '1979-10-15'],
                ['cabinet_id' => 19791015, 'title' => 'title', 'from' => '1979-10-15', 'to' => '1980-02-08'],
                ['cabinet_id' => 19800208, 'title' => 'title', 'from' => '1980-02-08', 'to' => '1983-05-26'],
                ['cabinet_id' => 19830526, 'title' => 'title', 'from' => '1983-05-26', 'to' => '1987-07-08'],
                ['cabinet_id' => 19870708, 'title' => 'title', 'from' => '1987-07-08', 'to' => '1988-09-28'],
                ['cabinet_id' => 19880928, 'title' => 'title', 'from' => '1988-09-28', 'to' => '1989-09-10'],
                ['cabinet_id' => 19890910, 'title' => 'title', 'from' => '1989-09-10', 'to' => '1991-04-30'],
                ['cabinet_id' => 19910430, 'title' => 'title', 'from' => '1991-04-30', 'to' => '1995-04-23'],
                ['cabinet_id' => 19950423, 'title' => 'title', 'from' => '1995-04-23', 'to' => '1999-05-28'],
                ['cabinet_id' => 19990528, 'title' => 'title', 'from' => '1999-05-28', 'to' => '2003-05-23'],
                ['cabinet_id' => 20030523, 'title' => 'title', 'from' => '2003-05-23', 'to' => '2004-09-15'],
                ['cabinet_id' => 20040915, 'title' => 'title', 'from' => '2004-09-15', 'to' => '2006-06-15'],
                ['cabinet_id' => 20060615, 'title' => 'title', 'from' => '2006-06-15', 'to' => '2007-05-24'],
                ['cabinet_id' => 20070524, 'title' => 'title', 'from' => '2007-05-24', 'to' => '2009-02-01'],
                ['cabinet_id' => 20090201, 'title' => 'title', 'from' => '2009-02-01', 'to' => '2009-05-10'],
                ['cabinet_id' => 20090510, 'title' => 'title', 'from' => '2009-05-10', 'to' => '2013-05-22'],
                ['cabinet_id' => 20130523, 'title' => 'title', 'from' => '2013-05-23', 'to' => '2016-04-07'],
                ['cabinet_id' => 20160407, 'title' => 'title', 'from' => '2016-04-07', 'to' => '2017-01-11'],
                ['cabinet_id' => 20170111, 'title' => 'title', 'from' => '2017-01-11', 'to' => '2017-11-30'],
                ['cabinet_id' => 20171130, 'title' => 'title', 'from' => '2017-11-30', 'to' => null],
            ]
        ]);
    }
}
