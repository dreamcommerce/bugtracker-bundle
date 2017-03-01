<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Collector;

use DreamCommerce\Component\Common\Exception\NotDefinedException;
use Psr\Log\LoggerInterface;

interface Psr3CollectorInterface extends CollectorInterface
{
    /**
     * @throws NotDefinedException
     *
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface;

    /**
     * @param LoggerInterface $logger
     *
     * @return $this
     */
    public function setLogger(LoggerInterface $logger);

    /**
     * @return bool
     */
    public function isFormatException(): bool;

    /**
     * @param bool $formatException
     *
     * @return $this
     */
    public function setFormatException(bool $formatException);
}
