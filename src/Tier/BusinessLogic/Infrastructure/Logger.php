<?php declare(strict_types=1);

/*
 * This file is part of the Digital Foundation packages.
 *
 * (c) Bagrat Hakobyan <b.a.hakobyan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DigitalFoundation\UtilBundle\BusinessLogic\Infrastructure;

use DateTime;
use Exception;


/**
 * Class Logger
 *
 * @author Bagrat Hakobyan <b.a.hakobyan@gmail.com>
 *
 * @package DigitalFoundation\UtilBundle\BusinessLogic\Infrastructure
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

    /** @var string */
    static private string $logFilePath;

    /** @var int */
    static private int $startTime = 0;
    /** @var float */
    static private float $startMicroTime = 0.0;
    /** @var int */
    static private int $timerMeasureType = self::TIMER_MEASURE_TYPE_SECONDS;
    /** @var string */
    static private string $logFileNamePrefix = "";
    /** @var string */
    static private string $delimiter = PHP_EOL;


    /**
     * @param string $logFileNamePrefix
     * @param int    $timerMeasureType
     * @param string $delimiter
     *
     * @return void
     */
    static public function init (
        string $logFileNamePrefix = "",
        int    $timerMeasureType  = self::TIMER_MEASURE_TYPE_ML_SECONDS,
        string $delimiter         = PHP_EOL
    ) : void {

        self::$logFileNamePrefix = $logFileNamePrefix;
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
     *
     * @return void
     * @throws Exception
     */
    static public function emergency (mixed $message) : void {
        self::log($message, self::TYPE_EMERGENCY);
    }

    /**
     * Action must be taken immediately.
     *
     * @param mixed $message
     *
     * @return void
     * @throws Exception
     */
    static public function alert (mixed $message) : void {
        self::log($message, self::TYPE_ALERT);
    }

    /**
     * Critical conditions.
     *
     * @param mixed $message
     *
     * @return void
     * @throws Exception
     */
    static public function critical (mixed $message) : void {
        self::log($message, self::TYPE_CRITICAL);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param mixed $message
     *
     * @return void
     * @throws Exception
     */
    static public function error (mixed $message) : void {
        self::log($message, self::TYPE_ERROR);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * @param mixed $message
     *
     * @return void
     * @throws Exception
     */
    static public function warning (mixed $message) : void {
        self::log($message, self::TYPE_WARNING);
    }

    /**
     * Normal but significant events.
     *
     * @param mixed $message
     *
     * @return void
     * @throws Exception
     */
    static public function notice (mixed $message) : void {
        self::log($message, self::TYPE_NOTICE);
    }

    /**
     * Interesting events.
     *
     * @param mixed $message
     *
     * @return void
     * @throws Exception
     */
    static public function info (mixed $message) : void {
        self::log($message);
    }

    /**
     * Detailed debug information.
     *
     * @param mixed $message
     *
     * @return void
     * @throws Exception
     */
    static public function debug (mixed $message) : void {
        self::log($message, self::TYPE_DEBUG);
    }

    /**
     * Logs with an arbitrary type.
     *
     * @param mixed  $message
     * @param string $type
     *
     * @return void
     * @throws Exception
     */
    static public function log (
        mixed  $message,
        string $type    = self::TYPE_INFO
    ) : void {

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
        mixed  $message,
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
    static private function addLog (mixed $message) : void {
        $dateTime     = (new DateTime())->format("Y-m-d H:i:s");
        $durationText = self::getDurationInfoText();
        $memoryUsage  = number_format(memory_get_usage() / (1024 * 1024), 2);

        $message .= " : Time - $dateTime, $durationText Memory usage - $memoryUsage MB" . self::$delimiter;

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

        $logDir = rtrim(Kernel::getLogDir(), "/") . "/" . ($folder ?? $type);

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