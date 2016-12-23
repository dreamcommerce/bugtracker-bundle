<?php

namespace DreamCommerce\Component\BugTracker\Collector;

use DreamCommerce\Component\BugTracker\Exception\NotDefinedException;

interface SwiftMailerCollectorInterface extends CollectorInterface
{
    /**
     * @throws NotDefinedException
     *
     * @return \Swift_Mailer
     */
    public function getMailer();

    /**
     * @param \Swift_Mailer $mailer
     *
     * @return $this
     */
    public function setMailer(\Swift_Mailer $mailer);

    /**
     * @throws NotDefinedException
     *
     * @return string
     */
    public function getSubject();

    /**
     * @param string $subject
     *
     * @return $this
     */
    public function setSubject($subject);

    /**
     * @throws NotDefinedException
     *
     * @return string
     */
    public function getSender();

    /**
     * @param string $sender
     *
     * @return $this
     */
    public function setSender($sender);

    /**
     * @return mixed
     */
    public function getRecipients();

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
    public function addRecipient($recipient);
}
