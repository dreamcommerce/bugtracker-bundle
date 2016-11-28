<?php

namespace DreamCommerce\Component\BugTracker\Collector;

use DreamCommerce\Component\BugTracker\Exception\InvalidArgumentException;
use Psr\Log\LoggerInterface;

interface Psr3CollectorInterface extends CollectorInterface
{
    /**
     * @return LoggerInterface
     */
    public function getLogger();

    /**
     * @param LoggerInterface $logger
     *
     * @return $this
     */
    public function setLogger(LoggerInterface $logger);

    /**
     * @return bool
     */
    public function isFormatException();

    /**
     * @param bool $formatException
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setFormatException($formatException);
}
