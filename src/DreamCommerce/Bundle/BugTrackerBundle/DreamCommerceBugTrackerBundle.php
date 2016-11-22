<?php

namespace DreamCommerce\Bundle\BugTrackerBundle;

use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Compiler\CollectorCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DreamCommerceBundleBugTrackerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new CollectorCompilerPass(), PassConfig::TYPE_BEFORE_REMOVING);
    }
}
