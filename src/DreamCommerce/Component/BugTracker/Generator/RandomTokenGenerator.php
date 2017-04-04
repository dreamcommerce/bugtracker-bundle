<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Generator;

use Throwable;

class RandomTokenGenerator implements TokenGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate(Throwable $exception, string $level, array $context = array()): string
    {
        return md5(uniqid(rand(), true));
    }
}
