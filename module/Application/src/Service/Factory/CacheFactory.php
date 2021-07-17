<?php
namespace Application\Service\Factory;

use Interop\Container\ContainerInterface;
use Laminas\Cache\StorageFactory;

final class CacheFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('configuration');
        return StorageFactory::factory($config['cache']);
    }
}
