<?php

namespace DreamCommerce\Bundle\BugTrackerBundle;

use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Compiler\CollectorCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class DreamCommerceBugTrackerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        // add Doctrine passes if available
        $this->addDoctrinePass($container);

        $container->addCompilerPass(new CollectorCompilerPass(), PassConfig::TYPE_BEFORE_REMOVING);
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function addDoctrinePass(ContainerBuilder $container)
    {
        if (class_exists('\Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass')) {
            $mappings = array(
                realpath(__DIR__.'/Resources/config/doctrine/model') => 'DreamCommerce\Component\BugTracker\Model',
            );

            $container->addCompilerPass(
                \Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass::createXmlMappingDriver($mappings)
            );
        }
    }
}
