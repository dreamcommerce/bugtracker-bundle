<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

use Doctrine\Annotations\AnnotationRegistry;

$loader = require __DIR__.'/../../vendor/autoload.php';

require __DIR__.'/AppKernel.php';

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

return $loader;
