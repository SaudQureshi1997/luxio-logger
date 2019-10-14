<?php

namespace Elphis\Utils;

use RuntimeException;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

use Psr\Log\InvalidArgumentException;

class Logger extends AbstractLogger
{
    private $logFilePath = null;
    private $filePointer = null;
    protected $config = [
        'extension'  => 'txt',
        'dateFormat' => 'Y-m-d G:i:s.u',
        'prefix'     => 'log_',
        'filename'   => false
    ];

    private $logLevels = [
        LogLevel::EMERGENCY => 0,
        LogLevel::ALERT     => 1,
        LogLevel::CRITICAL  => 2,
        LogLevel::ERROR     => 3,
        LogLevel::WARNING   => 4,
        LogLevel::NOTICE    => 5,
        LogLevel::INFO      => 6,
        LogLevel::DEBUG     => 7
    ];

    /**
     * Constructor which set-up the logger directory
     * and log file.
     * @param String  $logDirectory        Log Directory Path
     * @param integer $directoryPermission Log Directory Permission
     */
    public function __construct($logDirectory, $filename = false, $directoryPermission = 0777)
    {
        if (!file_exists($logDirectory)) {
            $status = mkdir($logDirectory, $directoryPermission, true);
            if ($status === false) {
                throw new RuntimeException('Unable to create the directory for the Log file.');
            }
        }
        if ($filename !== false) {
            $this->config['filename'] = rtrim($filename, DIRECTORY_SEPARATOR);
        }
        $this->setLogFilePath($logDirectory);
        $this->setFilePointer();
    }

    /**
     * Destructor which unsets
     * the Log File Pointer.
     */
    public function __destruct()
    {
        if ($this->filePointer !== null) {
            fclose($this->filePointer);
        }
    }

    /**
     * Function which sets the path of the Log
     * file.
     * @param String $logDirectory Log Directory Path
     */
    public function setLogFilePath($logDirectory)
    {
        $this->logFilePath = $logDirectory . DIRECTORY_SEPARATOR . $this->config['prefix'] . date('Y-m-d') . '.' . $this->config['extension'];
        if ($this->config['filename'] !== false) {
            $this->logFilePath = $logDirectory . DIRECTORY_SEPARATOR . $this->config['filename'];
        }
    }

    /**
     * This function innitialized the
     * file Pointer with the Log
     * file path.
     */
    public function setFilePointer()
    {
        $this->filePointer = fopen($this->logFilePath, 'a');
    }

    /**
     * This functions formats the message and
     * context in a format which can be
     * written into the log file.
     * @param  String  $message          String message
     * @param  Integer $logLevel         Log Level
     * @param  Array   $context          Context
     * @return String  $formattedMessage Formatted message
     */
    private function formatMessage($message, $logLevel, $context)
    {
        $logLevelPrefix = [
            0 => 'EMERGENCY',
            1 => 'ALERT',
            2 => 'CRITICAL',
            3 => 'ERROR',
            4 => 'WARNING',
            5 => 'NOTICE',
            6 => 'INFO',
            7 => 'DEBUG'
        ];
        $formattedMessage = '[' . date($this->config['dateFormat']) . ']';
        $formattedMessage = $formattedMessage . ' ' . $logLevelPrefix[$logLevel] . ': ';
        $formattedMessage = $formattedMessage . $message . PHP_EOL;
        if (!empty($context)) {
            $formattedMessage = $formattedMessage . json_encode($context) . PHP_EOL;
        }
        return $formattedMessage;
    }

    /**
     * The log function which writes the log
     * message in the log file.
     * @param  String  $message  String message
     * @param  Integer $logLevel Log Level
     * @param  array   $context  Log Context
     */
    public function log($message, $logLevel = LogLevel::DEBUG, array $context = [])
    {
        if (!in_array($logLevel, $this->logLevels)) {
            throw new InvalidArgumentException('The provided Log Level is invalid');
        }
        $message = $this->formatMessage($message, $logLevel, $context);
        if (fwrite($this->filePointer, $message) === false) {
            throw new RuntimeException('Unable to write to the Log file.');
        }
    }
}
