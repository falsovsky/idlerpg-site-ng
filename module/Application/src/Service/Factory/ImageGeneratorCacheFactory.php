<?php
namespace Application\Service\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Application\Service\ImageGenerator;
use Application\Service\ImageGeneratorCache;

class ImageGeneratorCacheFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ) {
        $imageGenerator = $container->get(ImageGenerator::class);

        $cache = $container->get('Cache');
        $cache->clearExpired();

        return new ImageGeneratorCache($imageGenerator, $cache);
    }
}
