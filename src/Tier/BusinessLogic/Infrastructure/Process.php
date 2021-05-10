<?php

namespace DigitalFoundation\Basement\UtilBundle\BusinessLogic\BusinessInfrastructure;

/**
 * Class Process
 *
 * @author Bagrat Hakobyan <b.a.hakobyan@gmail.com>
 *
 * @package DigitalFoundation\Basement\UtilBundle\BusinessLogic\BusinessInfrastructure
 */
abstract class Process {

    /**
     * @param int $value
     *
     * @return void
     */
    static public function setMemoryLimit(int $value): void {
        ini_set("memory_limit", "{$value}M");
    }

    /**
     * @param int $seconds
     *
     * @return void
     */
    static public function setTimeLimit(int $seconds): void {
        set_time_limit($seconds);
    }

    /**
     * @param int $memoryLimit
     *
     * @return void
     */
    static public function initBigProcess(int $memoryLimit = 4096): void {
        self::setMemoryLimit($memoryLimit);
    }

    /**
     * @param int $timeLimit
     *
     * @return void
     */
    static public function initLongProcess(int $timeLimit = 0): void {
        self::setTimeLimit($timeLimit);
    }

    /**
     * @param int $memoryLimit
     * @param int $timeLimit
     *
     * @return void
     */
    static public function initBigLongProcess(int $memoryLimit = 4096, int $timeLimit = 0): void {
        self::initBigProcess($memoryLimit);
        self::initLongProcess($timeLimit);
    }
}