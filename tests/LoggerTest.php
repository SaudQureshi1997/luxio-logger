<?php

use Elphis\Logger;

class LoggerTest extends PHPUnit\Framework\TestCase
{
    private $logger;
    private $directory = __DIR__ . '/logs/';

    /**
     * Setup function of TestCase class
     * which setups objects for ascertions.
     */
    public function setUp(): void
    {
        $this->logger = new Logger($this->directory);
    }

    /**
     * This function tests the PSR3
     * standard.
     */
    public function testPsr3()
    {
        $this->assertInstanceOf('Psr\Log\LoggerInterface', $this->logger);
    }

    /**
     * This function tests the directory
     * creation in the test folder.
     */
    public function testDirectoryCreation()
    {
        $this->assertDirectoryExists($this->directory);
    }

    /**
     * This function tests the default
     * Log file creation.
     */
    public function testDefaultLogFileCreation()
    {
        $file_name = $this->directory . DIRECTORY_SEPARATOR . 'log_' . date('Y-m-d') . '.txt';
        $this->assertFileExists($file_name);
    }

    /**
     * This function test the customlog
     * file creation.
     */
    public function testCustomLogFileCreation()
    {
        $file_name = 'Custom_Log_File.md';
        $logger = new Logger($this->directory, $file_name);
        $file_name = $this->directory . DIRECTORY_SEPARATOR . $file_name;
        $this->assertFileExists($file_name);
    }

    /**
     * This function tests the exception
     * for invalid Log Level.
     */
    public function testException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->logger->log("This should generate Exception", 450);
    }

    /**
     * tearDown function of TestCase class
     * which deletes the test directory.
     */
    public function tearDown(): void
    {
        @unlink($this->directory);
    }
}
