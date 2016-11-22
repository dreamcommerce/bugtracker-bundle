<?php

namespace DreamCommerce\Component\BugTracker\Collector;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Psr3Collector extends BaseCollector implements Psr3CollectorInterface
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
     * @param array           $options
     */
    public function __construct(LoggerInterface $logger, array $options = array())
    {
        $this->_logger = $logger;
        parent::__construct($options);
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
     * {@inheritdoc}
     */
    public function getLogger()
    {
        return $this->_logger;
    }

    /**
     * {@inheritdoc}
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->_logger = $logger;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isFormatException()
    {
        return $this->_formatException;
    }

    /**
     * {@inheritdoc}
     */
    public function setFormatException($formatException)
    {
        $this->_formatException = (bool) $formatException;

        return $this;
    }
}
