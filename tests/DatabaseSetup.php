<?php

namespace Althingi;

use Exception;
use PHPUnit_Framework_AssertionFailedError;
use PHPUnit_Framework_Test;
use PHPUnit_Framework_TestListener;
use PHPUnit_Framework_TestSuite;
use PHPUnit_Extensions_Database_TestCase;

class DatabaseSetup implements PHPUnit_Framework_TestListener
{
    /** @var string  */
    private $dbUser;

    /** @var string  */
    private $dbName;

    /** @var string  */
    private $dbTestName;

    /** @var string  */
    private $dbPass;

    /** @var string  */
    private $dbBin;

    /** @var string  */
    private $dbDumpBin;

    /** @var bool */
    private $hasDatabase = false;

    /**
     * DatabaseSetup constructor.
     * Initialize local variables
     */
    public function __construct()
    {
        $this->dbName = getenv('DB_NAME') ? : 'althingi';
        $this->dbTestName = 'althingi_test';
        $this->dbUser = getenv('DB_USER') ? : 'root';
        $this->dbPass = getenv('DB_PASSWORD') ? : '';
        $this->dbBin = $GLOBALS['MYSQL.BIN'] ? : 'mysql';
        $this->dbDumpBin = $GLOBALS['MYSQLDUMP.BIN'] ? : 'mysqldump';
    }

    /**
     * An error occurred.
     *
     * @param PHPUnit_Framework_Test $test
     * @param Exception $e
     * @param float $time
     */
    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        // TODO: Implement addError() method.
    }

    /**
     * A failure occurred.
     *
     * @param PHPUnit_Framework_Test $test
     * @param PHPUnit_Framework_AssertionFailedError $e
     * @param float $time
     */
    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        // TODO: Implement addFailure() method.
    }

    /**
     * Incomplete test.
     *
     * @param PHPUnit_Framework_Test $test
     * @param Exception $e
     * @param float $time
     */
    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        // TODO: Implement addIncompleteTest() method.
    }

    /**
     * Risky test.
     *
     * @param PHPUnit_Framework_Test $test
     * @param Exception $e
     * @param float $time
     *
     * @since  Method available since Release 4.0.0
     */
    public function addRiskyTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        // TODO: Implement addRiskyTest() method.
    }

    /**
     * Skipped test.
     *
     * @param PHPUnit_Framework_Test $test
     * @param Exception $e
     * @param float $time
     *
     * @since  Method available since Release 3.0.0
     */
    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        // TODO: Implement addSkippedTest() method.
    }

    /**
     * A test suite started.
     *
     * @param PHPUnit_Framework_TestSuite $suite
     *
     * @since  Method available since Release 2.2.0
     */
    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        foreach ($suite->tests() as $test) {
            if ($test instanceof PHPUnit_Extensions_Database_TestCase) {
                $this->setupDatabase();
                break;
            }
        }
    }

    /**
     * A test suite ended.
     *
     * @param PHPUnit_Framework_TestSuite $suite
     *
     * @since  Method available since Release 2.2.0
     */
    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $this->teardownDatabase();
    }

    /**
     * A test started.
     *
     * @param PHPUnit_Framework_Test $test
     */
    public function startTest(PHPUnit_Framework_Test $test)
    {
        // TODO: Implement startTest() method.
    }

    /**
     * A test ended.
     *
     * @param PHPUnit_Framework_Test $test
     * @param float $time
     */
    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        // TODO: Implement endTest() method.
    }

    private function setupDatabase()
    {
        exec($this->dbBin.' -u root -e "drop database if exists '.
            $this->dbTestName.'; create database '.
            $this->dbTestName.';" && '.$this->dbDumpBin.' -u '.$this->dbUser.' -d '.
            $this->dbName.' | '.$this->dbBin.' -u '.$this->dbUser.' -D '.
            $this->dbTestName);
        $this->hasDatabase = true;
    }

    private function teardownDatabase()
    {
        $this->hasDatabase = false;
    }

    public function __destruct()
    {
        $this->teardownDatabase();
    }
}
