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


/**
 * Class Process
 *
 * @author Bagrat Hakobyan <b.a.hakobyan@gmail.com>
 *
 * @package DigitalFoundation\UtilBundle\BusinessLogic\Infrastructure
 */
abstract class Process {

    /**
     * @param int $memoryLimit
     *
     * @return void
     */
    static public function setMemoryLimit(int $memoryLimit): void {
        ini_set("memory_limit", "{$memoryLimit}M");
    }

    /**
     * @param int $timeLimit
     *
     * @return void
     */
    static public function setTimeLimit(int $timeLimit): void {
        set_time_limit($timeLimit);
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