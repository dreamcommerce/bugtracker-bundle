<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Doctrine\DBAL\Types;

use DreamCommerce\Component\Common\Doctrine\DBAL\Types\MapEnumType;
use Psr\Log\LogLevel;

final class LogLevelUInt16Type extends MapEnumType
{
    const TYPE_NAME = 'enumLogLevelUInt16Type';

    /**
     * @var string
     */
    protected $enumType = self::TYPE_UINT16;

    /**
     * @var string
     */
    protected $name = self::TYPE_NAME;

    /**
     * @var array
     */
    protected $values = array(
        LogLevel::EMERGENCY => 7,
        LogLevel::ALERT => 6,
        LogLevel::CRITICAL => 5,
        LogLevel::ERROR => 4,
        LogLevel::WARNING => 3,
        LogLevel::NOTICE => 2,
        LogLevel::INFO => 1,
        LogLevel::DEBUG => 0
    );
}
