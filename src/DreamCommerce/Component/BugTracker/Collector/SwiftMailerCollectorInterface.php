<?php

/**
 * (c) 2017-2020 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Collector;

use DreamCommerce\Component\Common\Exception\NotDefinedException;
use Swift_Mailer;

interface SwiftMailerCollectorInterface extends CollectorInterface
{
    /**
     * @throws NotDefinedException
     *
     * @return Swift_Mailer
     */
    public function getMailer(): Swift_Mailer;

    /**
     * @param Swift_Mailer $mailer
     *
     * @return $this
     */
    public function setMailer(Swift_Mailer $mailer);

    /**
     * @throws NotDefinedException
     *
     * @return string
     */
    public function getSubject(): string;

    /**
     * @param string $subject
     *
     * @return $this
     */
    public function setSubject(string $subject);

    /**
     * @throws NotDefinedException
     *
     * @return string
     */
    public function getSender(): string;

    /**
     * @param string $sender
     *
     * @return $this
     */
    public function setSender(string $sender);

    /**
     * @return array
     */
    public function getRecipients(): array;

    /**
     * @param array $recipients
     *
     * @return $this
     */
    public function setRecipients(array $recipients = array());

    /**
     * @param string $recipient
     *
     * @return $this
     */
    public function addRecipient(string $recipient);
}
