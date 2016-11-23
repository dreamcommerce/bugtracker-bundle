<?php

namespace DreamCommerce\Component\BugTracker\Collector;

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
     *
     * @return $this
     */
    public function setFormatException($formatException);
}