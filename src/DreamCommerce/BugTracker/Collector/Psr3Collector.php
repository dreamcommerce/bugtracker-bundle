<?php

namespace DreamCommerce\BugTracker\Collector;

use DreamCommerce\BugTracker\BugHandler;
use DreamCommerce\BugTracker\Exception\RuntimeException;
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
     * {@inheritdoc}
     */
    protected function _handle($exc, $level = LogLevel::WARNING, array $context = array())
    {
        if(!is_object($exc)) {
            throw new RuntimeException('Unsupported type of variable (expected: object; got: ' . gettype($exc) . ')');
        }

        if(!($exc instanceof \Exception) && !($exc instanceof \Throwable)) {
            throw new RuntimeException('Unsupported class of object (expected: \Exception|\Throwable; got: ' . get_class($exc) . ')');
        }

        $context = array_merge($context, BugHandler::getContext($exc));
        if($this->_formatException) {
            $exc = "exception '" . get_class($exc) . "' with message '" . $exc->getMessage() . "' in '" . $exc->getFile() . ':' . $exc->getLine() . ' Stack trace: ' . $exc->getTraceAsString();
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
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->_logger = $logger;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isFormatException()
    {
        return $this->_formatException;
    }

    /**
     * @param boolean $formatException
     * @return $this
     */
    public function setFormatException($formatException)
    {
        $this->_formatException = (bool)$formatException;
        return $this;
    }
}