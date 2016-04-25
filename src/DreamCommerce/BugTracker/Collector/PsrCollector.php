<?php

namespace DreamCommerce\BugTracker\Collector;

use Psr\Log\LoggerInterface;

class PsrCollector extends BaseCollector
{
    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->_logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($exc, $level, array $context = array())
    {
        $this->_logger->log($level, $exc, $context);
        $this->_isCollected = true;
    }
}