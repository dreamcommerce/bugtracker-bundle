<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Bundle\BugTrackerBundle;

use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Compiler\CollectorCompilerPass;
use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Compiler\ExtensionCompilerPass;
use DreamCommerce\Component\BugTracker\Doctrine\DBAL\Types\LogLevelEnumType;
use DreamCommerce\Component\BugTracker\Doctrine\DBAL\Types\LogLevelUInt16Type;
use DreamCommerce\Component\BugTracker\Doctrine\DBAL\Types\LogLevelUInt8Type;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class DreamCommerceBugTrackerBundle extends Bundle
{
    public function boot()
    {
        parent::boot();

        if (class_exists('\Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass')) {
            /** @var \Doctrine\Bundle\DoctrineBundle\Registry $registry */
            $registry = $this->container->get('doctrine');
            /** @var \Doctrine\DBAL\Connection $connection */
            foreach ($registry->getConnections() as $connection) {
                $platform = $connection->getDatabasePlatform();

                $types = array(
                    LogLevelEnumType::TYPE_NAME => LogLevelEnumType::class,
                    LogLevelUInt8Type::TYPE_NAME => LogLevelUInt8Type::class,
                    LogLevelUInt16Type::TYPE_NAME => LogLevelUInt16Type::class,
                );

                foreach ($types as $type => $className) {
                    if (!\Doctrine\DBAL\Types\Type::hasType($type)) {
                        \Doctrine\DBAL\Types\Type::addType($type, $className);
                        $platform->registerDoctrineTypeMapping($type, $type);
                    }
                }
            }
        }
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        // add Doctrine passes if available
        $this->addDoctrinePass($container);

        $container->addCompilerPass(new CollectorCompilerPass(), PassConfig::TYPE_BEFORE_REMOVING);
        $container->addCompilerPass(new ExtensionCompilerPass());
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
