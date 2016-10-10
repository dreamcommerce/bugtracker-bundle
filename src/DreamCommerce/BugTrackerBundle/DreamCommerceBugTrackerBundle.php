<?php

namespace DreamCommerce\BugTrackerBundle;

use DreamCommerce\BugTrackerBundle\DependencyInjection\Compiler\CollectorCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DreamCommerceBugTrackerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new CollectorCompilerPass(), PassConfig::TYPE_BEFORE_REMOVING);
    }
}