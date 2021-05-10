<?php

namespace DigitalFoundation\Basement\UtilBundle\BusinessLogic\BusinessInfrastructure;

/**
 * Class UniqueID
 *
 * @author Bagrat Hakobyan <b.a.hakobyan@gmail.com>
 *
 * @package DigitalFoundation\Basement\UtilBundle\BusinessLogic\BusinessInfrastructure
 */
abstract class UniqueID {

    /** @var int */
    public const TYPE_INTEGER = 1; // TODO: Add in future.

    /** @var int */
    public const TYPE_STRING  = 2;

    /**
     * @return string
     */
    static public function generateId() : string {
        return md5(rand(0, 999999) . uniqid(rand(0, 999999), true) . rand(0, 999999));
    }
}