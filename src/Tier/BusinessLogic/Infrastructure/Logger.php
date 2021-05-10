<?php

namespace DigitalFoundation\Basement\UtilBundle\BusinessLogic\BusinessInfrastructure;

use DateTime;
use Exception;

/**
 * Class Logger
 *
 * @author Bagrat Hakobyan <b.a.hakobyan@gmail.com>
 *
 * @package DigitalFoundation\Basement\UtilBundle\BusinessLogic\BusinessInfrastructure
 */
abstract class Logger {

    public const TYPE_EMERGENCY = "emergency";
    public const TYPE_ALERT     = "alert";
    public const TYPE_CRITICAL  = "critical";
    public const TYPE_ERROR     = "error";
    public const TYPE_WARNING   = "warning";
    public const TYPE_NOTICE    = "notice";
    public const TYPE_INFO      = "info";
    public const TYPE_DEBUG     = "debug";

    public const TIMER_MEASURE_TYPE_SECONDS    = 1;
    public const TIMER_MEASURE_TYPE_ML_SECONDS = 2;

    /** @var array */
    static private $context;
    /** @var string */
    static private $logFilePath;

    /** @var string */
    static private $logType = self::TYPE_INFO;
    /** @var int */
    static private $startTime = 0;
    /** @var float */
    static private $startMicroTime = 0.0;
    /** @var int */
    static private $timerMeasureType = self::TIMER_MEASURE_TYPE_SECONDS;
    /** @var string */
    static private $logFileNamePrefix = "";
    /** @var string */
    static private $delimiter = PHP_EOL;


    /**
     * @param string $logFileNamePrefix
     * @param string $logType
     * @param int    $timerMeasureType
     * @param string $delimiter
     *
     * @return void
     */
    static public function init (
        string $logFileNamePrefix = "",
        string $logType           = self::TYPE_INFO,
        int    $timerMeasureType  = self::TIMER_MEASURE_TYPE_ML_SECONDS,
        string $delimiter         = PHP_EOL
    ) : void {

        self::$logFileNamePrefix = $logFileNamePrefix;
        self::$logType           = $logType;
        self::$timerMeasureType  = $timerMeasureType;
        self::$delimiter         = $delimiter;
    }

    /**
     * @return void
     * @throws Exception
     */
    static public function start () : void {
        if ( self::$timerMeasureType == self::TIMER_MEASURE_TYPE_SECONDS ) {
            self::$startTime = time();
        } else {
            self::$startMicroTime = microtime(true);
        }

        self::setLogFilePath();
        file_put_contents(self::$logFilePath, self::$delimiter, FILE_APPEND);
    }

    /**
     * @return void
     */
    static public function end () : void {
        self::$startTime = 0;
        self::$startMicroTime = 0;
    }

    /**
     * System is unusable.
     *
     * @param mixed $message
     * @param array $context
     *
     * @return void
     * @throws Exception
     */
    static public function emergency ($message, array $context = []) : void {
        self::log($message, self::TYPE_EMERGENCY); self::$context = $context;
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param mixed $message
     * @param array $context
     *
     * @return void
     * @throws Exception
     */
    static public function alert ($message, array $context = []) : void {
        self::log($message, self::TYPE_ALERT); self::$context = $context;
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param mixed $message
     * @param array $context
     *
     * @return void
     * @throws Exception
     */
    static public function critical ($message, array $context = []) : void {
        self::log($message, self::TYPE_CRITICAL); self::$context = $context;
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param mixed $message
     * @param array $context
     *
     * @return void
     * @throws Exception
     */
    static public function error ($message, array $context = []) : void {
        self::log($message, self::TYPE_ERROR); self::$context = $context;
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param mixed $message
     * @param array $context
     *
     * @return void
     * @throws Exception
     */
    static public function warning ($message, array $context = []) : void {
        self::log($message, self::TYPE_WARNING); self::$context = $context;
    }

    /**
     * Normal but significant events.
     *
     * @param mixed $message
     * @param array $context
     *
     * @return void
     * @throws Exception
     */
    static public function notice ($message, array $context = []) : void {
        self::log($message, self::TYPE_NOTICE); self::$context = $context;
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param mixed $message
     * @param array $context
     *
     * @return void
     * @throws Exception
     */
    static public function info ($message, array $context = []) : void {
        self::log($message, self::TYPE_INFO); self::$context = $context;
    }

    /**
     * Detailed debug information.
     *
     * @param mixed $message
     * @param array $context
     *
     * @return void
     * @throws Exception
     */
    static public function debug ($message, array $context = []) : void {
        self::log($message, self::TYPE_DEBUG); self::$context = $context;
    }

    /**
     * Logs with an arbitrary type.
     *
     * @param mixed  $message
     * @param string $type
     * @param array  $context
     *
     * @return void
     * @throws Exception
     */
    static public function log (
               $message,
        string $type    = self::TYPE_INFO,
        array  $context = []
    ) : void {

        self::$context = $context;

        self::setLogFilePath($type);
        self::addLog($message);
    }

    /**
     * Logs with an arbitrary type to required folder.
     *
     * @param mixed  $message
     * @param string $folder
     * @param string $type
     *
     * @return void
     * @throws Exception
     */
    static public function logToFolder (
               $message,
        string $folder,
        string $type = self::TYPE_INFO
    ) : void {

        self::setLogFilePath($type, $folder);
        self::addLog($message);
    }

    /**
     * @param mixed $message
     *
     * @return void
     * @throws Exception
     */
    static private function addLog ($message) : void {
        $dateTime     = (new DateTime())->format("Y-m-d H:i:s");
        $durationText = self::getDurationInfoText();
        $memoryUsage  = number_format(memory_get_usage() / (1024 * 1024), 2);

        $message .= " : Time - {$dateTime}, {$durationText} Memory usage - {$memoryUsage} MB" . self::$delimiter; // TODO: Add more log formats.

        file_put_contents(self::$logFilePath, $message, FILE_APPEND);
    }

    /**
     * @param string      $type
     * @param string|null $folder
     *
     * @return void
     * @throws Exception
     */
    static private function setLogFilePath (
        string  $type   = self::TYPE_INFO,
        ?string $folder = null
    ) : void {

        $logDir = Kernel::getLogDir() . ($folder ?? $type);

        if ( !file_exists($logDir) ) {
            mkdir($logDir, 0777, true);
        }

        $logFileNamePrefix = "";
        if ( !empty(self::$logFileNamePrefix) ) {
            $logFileNamePrefix = self::$logFileNamePrefix . "_";
        }

        $filePath = $logDir . "/" . $logFileNamePrefix . (new DateTime())->format("Y-m-d") . ".log";

        self::$logFilePath = $filePath;
    }

    /**
     * @return string
     */
    static private function getDurationInfoText () : string {
        if ( empty(self::$startMicroTime) && empty(self::$startTime) ) {
            return "";
        }

        $infoText = "Duration - ";

        if ( self::$timerMeasureType == self::TIMER_MEASURE_TYPE_SECONDS ) {
            $duration = (time() - self::$startTime);
            $infoText .= $duration . " sc, ";
        } else {
            $duration = number_format((microtime(true) - self::$startMicroTime) * 1000, 4);
            $infoText .= $duration . " msc, ";
        }

        return $infoText;
    }
}