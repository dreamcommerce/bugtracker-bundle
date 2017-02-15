<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Collector;

use DreamCommerce\Component\BugTracker\Exception\NotDefinedException;
use Psr\Log\LogLevel;
use Webmozart\Assert\Assert;

class SwiftMailerCollector extends BaseCollector implements SwiftMailerCollectorInterface
{
    const SEPARATOR = '-----------------------------------------------------------';

    /**
     * @var \Swift_Mailer
     */
    protected $_mailer;

    /**
     * @var string
     */
    protected $_subject;

    /**
     * @var string
     */
    protected $_sender;

    /**
     * @var array
     */
    protected $_recipients = array();

    /**
     * {@inheritdoc}
     */
    protected function _handle($exception, $level = LogLevel::WARNING, array $context = array())
    {
        $message = \Swift_Message::newInstance();
        $this->_fillModel($message, $exception, $level, $context);
        $this->_mailer->send($message);
    }

    /**
     * @param \Swift_Message        $message
     * @param \Exception|\Throwable $exc
     * @param int                   $level
     * @param array                 $context
     */
    protected function _fillModel(\Swift_Message $message, $exc, $level, array $context = array())
    {
        $token = null;
        $subject = $this->getSubject();

        if ($this->isUseToken()) {
            $token = $this->getTokenGenerator()->generate($exc, $level, $context);
            $subject .= ' [ '.$token.' ]';
        }

        $body =
            static::SEPARATOR.PHP_EOL.
            date('Y-m-d H:i:s').PHP_EOL.
            static::SEPARATOR.PHP_EOL.PHP_EOL;

        if ($this->isUseToken()) {
            $body .= 'Token: '.$token.PHP_EOL;
        }

        $body .= 'Message: '.$exc->getMessage().PHP_EOL.
            'File: '.$exc->getFile().' (line: '.$exc->getLine().')'.PHP_EOL.
            static::SEPARATOR.PHP_EOL;

        if (count($context) > 0) {
            $body .= 'Parameters:'.PHP_EOL.PHP_EOL.
                $this->_prepareContext($context).PHP_EOL.
                static::SEPARATOR.PHP_EOL;
        }

        $body .= 'Stack trace:'.PHP_EOL.PHP_EOL.$exc->getTraceAsString();

        $message
            ->setSubject($subject)
            ->setFrom($this->getSender())
            ->setTo($this->getRecipients())
            ->setBody(
                $body,
                'text/plain'
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getMailer()
    {
        if ($this->_mailer === null) {
            throw new NotDefinedException(__CLASS__.'::_mailer');
        }

        return $this->_mailer;
    }

    /**
     * {@inheritdoc}
     */
    public function setMailer(\Swift_Mailer $mailer)
    {
        $this->_mailer = $mailer;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        if ($this->_subject === null) {
            throw new NotDefinedException(__CLASS__.'::_subject');
        }

        return $this->_subject;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubject($subject)
    {
        $this->_subject = $subject;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSender()
    {
        if ($this->_sender === null) {
            throw new NotDefinedException(__CLASS__.'::_sender');
        }

        return $this->_sender;
    }

    /**
     * {@inheritdoc}
     */
    public function setSender($sender)
    {
        $this->_sender = $sender;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecipients()
    {
        return $this->_recipients;
    }

    /**
     * {@inheritdoc}
     */
    public function setRecipients(array $recipients = array())
    {
        Assert::notEmpty($recipients);

        $parsed = array();
        foreach ($recipients as $recipient) {
            $parsed[] = strtolower($recipient);
        }
        $this->_recipients = array_unique($parsed);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addRecipient($recipient)
    {
        Assert::string($recipient);

        $recipient = strtolower($recipient);
        if (!in_array($recipient, $this->_recipients)) {
            $this->_recipients[] = $recipient;
        }

        return $this;
    }

    protected function _prepareContext(array $array, $prefix = "\t")
    {
        $result = '';

        foreach ($array as $key => $value) {
            $result .= $key.': ';
            if (is_object($value)) {
                $value = '<'.get_class($value).'>';
            }
            if (is_array($value)) {
                $result .= PHP_EOL.static::_prepareContext($value, $prefix."\t");
            } else {
                $result .= $value;
            }
            $result .= PHP_EOL;
        }

        return $result;
    }
}
