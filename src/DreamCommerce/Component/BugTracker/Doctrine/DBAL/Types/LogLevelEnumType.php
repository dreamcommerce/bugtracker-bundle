<?php

/**
 * (c) 2017-2020 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Doctrine\DBAL\Types;

use DreamCommerce\Component\Common\Doctrine\DBAL\Types\EnumType;
use Psr\Log\LogLevel;

final class LogLevelEnumType extends EnumType
{
    const TYPE_NAME = 'enumLogLevelType';

    /**
     * @var string
     */
    protected $name = self::TYPE_NAME;

    /**
     * @var array
     */
    protected $values = array(
        LogLevel::EMERGENCY,
        LogLevel::ALERT,
        LogLevel::CRITICAL,
        LogLevel::ERROR,
        LogLevel::WARNING,
        LogLevel::NOTICE,
        LogLevel::INFO,
        LogLevel::DEBUG
    );
}
