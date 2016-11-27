<?php

namespace DreamCommerce\Component\BugTracker\Collector;

use DreamCommerce\Component\BugTracker\Generator\TokenGeneratorInterface;

interface TokenAwareInterface
{
    /**
     * @return TokenGeneratorInterface|null
     */
    public function getTokenGenerator();

    /**
     * @param TokenGeneratorInterface|null $tokenGenerator
     * @return $this
     */
    public function setTokenGenerator(TokenGeneratorInterface $tokenGenerator = null);

    /**
     * @return boolean
     */
    public function isUseToken();

    /**
     * @param boolean $useToken
     * @return $this;
     */
    public function setUseToken($useToken);
}