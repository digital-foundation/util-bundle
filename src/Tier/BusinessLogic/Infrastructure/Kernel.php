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

use App\Kernel as AppKernel;


/**
 * Class Kernel
 *
 * @author Bagrat Hakobyan <b.a.hakobyan@gmail.com>
 *
 * @package DigitalFoundation\UtilBundle\BusinessLogic\Infrastructure
 */
abstract class Kernel {

    /** @var AppKernel|null */
    static private ?AppKernel $kernel = null;

    /**
     * @return string
     */
    static public function getProjectDir () : string {
        self::initKernel();

        return self::$kernel->getProjectDir();
    }

    /**
     * @return string
     */
    static public function getCacheDir () : string {
        self::initKernel();

        return self::$kernel->getCacheDir();
    }

    /**
     * @return string
     */
    static public function getLogDir () : string {
        self::initKernel();

        return self::$kernel->getLogDir();
    }

    /**
     * @return void
     */
    static private function initKernel () : void {
        if ( self::$kernel === null ) {
            self::$kernel = new AppKernel($_SERVER["APP_ENV"], $_SERVER["APP_DEBUG"]);
        }
    }
}