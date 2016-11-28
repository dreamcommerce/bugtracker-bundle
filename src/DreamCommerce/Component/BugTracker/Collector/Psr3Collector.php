<?php

namespace DreamCommerce\Component\BugTracker\Collector;

use DreamCommerce\Component\BugTracker\Assert;
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
     * {@inheritdoc}
     */
    protected function _handle($exc, $level = LogLevel::WARNING, array $context = array())
    {
        $token = null;
        if ($this->isUseToken()) {
            $token = $this->getTokenGenerator()->generate($exc, $level, $context);
        }

        if ($this->_formatException) {
            $exc = '';
            if ($this->isUseToken()) {
                $exc .= '[ '.$token.' ] ';
            }
            $exc .= "exception '".get_class($exc)."' with message '".$exc->getMessage()."' in '".$exc->getFile().':'.$exc->getLine().' Stack trace: '.$exc->getTraceAsString();
        } elseif ($this->isUseToken()) {
            $context['token'] = $token;
        }

        $this->_logger->log($level, $exc, $context);

        $this->setIsCollected(true);
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
        Assert::boolean($formatException);

        $this->_formatException = $formatException;

        return $this;
    }
}
