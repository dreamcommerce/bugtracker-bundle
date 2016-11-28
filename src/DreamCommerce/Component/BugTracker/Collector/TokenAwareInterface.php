<?php

namespace DreamCommerce\Component\BugTracker\Collector;

use DreamCommerce\Component\BugTracker\Exception\InvalidArgumentException;
use DreamCommerce\Component\BugTracker\Exception\NotDefinedException;
use DreamCommerce\Component\BugTracker\Generator\TokenGeneratorInterface;

interface TokenAwareInterface
{
    /**
     * @throws NotDefinedException
     *
     * @return TokenGeneratorInterface
     */
    public function getTokenGenerator();

    /**
     * @param TokenGeneratorInterface $tokenGenerator
     *
     * @return $this
     */
    public function setTokenGenerator(TokenGeneratorInterface $tokenGenerator);

    /**
     * @return bool
     */
    public function isUseToken();

    /**
     * @param bool $useToken
     *
     * @throws InvalidArgumentException
     *
     * @return $this;
     */
    public function setUseToken($useToken);
}
