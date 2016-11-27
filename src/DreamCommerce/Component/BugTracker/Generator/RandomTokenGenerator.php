<?php

namespace DreamCommerce\Component\BugTracker\Generator;

class RandomTokenGenerator implements TokenGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate($exc, $level, array $context = array())
    {
        return md5(uniqid(rand(), true));
    }
}
