<?php

/**
 * (c) 2017-2020 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Exception;

use Exception;

class UnableGenerateTokenException extends Exception
{
    const CODE_FOR_EMPTY_CONTEXT = 10;

    /**
     * @return UnableGenerateTokenException
     */
    public static function forEmptyContext(): UnableGenerateTokenException
    {
        return new static('Unable generate token from empty context', static::CODE_FOR_EMPTY_CONTEXT);
    }
}
