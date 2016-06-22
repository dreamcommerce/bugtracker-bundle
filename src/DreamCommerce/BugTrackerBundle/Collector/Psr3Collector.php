<?php

namespace DreamCommerce\BugTrackerBundle\Collector;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Psr3Collector extends BaseCollector
{
    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var bool
     */
    protected $_formatException = true;

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
    protected function _handle($exc, $level = LogLevel::WARNING, array $context = array())
    {
        if ($this->_formatException) {
            $exc = "exception '".get_class($exc)."' with message '".$exc->getMessage()."' in '".$exc->getFile().':'.$exc->getLine().' Stack trace: '.$exc->getTraceAsString();
        }
        unset($context['message']);
        unset($context['code']);
        unset($context['file']);
        unset($context['line']);

        $this->_logger->log($level, $exc, $context);
        $this->_isCollected = true;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->_logger;
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->_logger = $logger;

        return $this;
    }

    /**
     * @return bool
     */
    public function isFormatException()
    {
        return $this->_formatException;
    }

    /**
     * @param bool $formatException
     *
     * @return $this
     */
    public function setFormatException($formatException)
    {
        $this->_formatException = (bool) $formatException;

        return $this;
    }
}
