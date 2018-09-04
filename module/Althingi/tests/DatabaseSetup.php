<?php

namespace AlthingiTest;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;

class DatabaseSetup implements TestListener
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

    public function __destruct()
    {
        $this->tearDownDatabase();
    }

    public function addWarning(Test $test, Warning $e, float $time): void
    {
        // TODO: Implement addWarning() method.
    }

    public function addError(Test $test, \Throwable $t, float $time): void
    {
        // TODO: Implement addError() method.
    }

    public function addFailure(Test $test, AssertionFailedError $e, float $time): void
    {
        // TODO: Implement addFailure() method.
    }

    public function addIncompleteTest(Test $test, \Throwable $t, float $time): void
    {
        // TODO: Implement addIncompleteTest() method.
    }

    public function addRiskyTest(Test $test, \Throwable $t, float $time): void
    {
        // TODO: Implement addRiskyTest() method.
    }

    public function addSkippedTest(Test $test, \Throwable $t, float $time): void
    {
        // TODO: Implement addSkippedTest() method.
    }

    public function startTestSuite(TestSuite $suite): void
    {
        if (strtolower(getenv('DB_SETUP')) === 'false') {
        } else {
            foreach ($suite->tests() as $test) {
                if (in_array(DatabaseConnection::class, class_uses($test))) {
                    $this->setupDatabase();
                    break;
                }
            }
        }

    }

    public function endTestSuite(TestSuite $suite): void
    {
        if (strtolower(getenv('DB_SETUP')) === 'false') {
        } else {
            $this->tearDownDatabase();
        }
    }

    public function startTest(Test $test): void
    {
        // TODO: Implement startTest() method.
    }

    public function endTest(Test $test, float $time): void
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

    private function tearDownDatabase()
    {
        $this->hasDatabase = false;
    }
}
