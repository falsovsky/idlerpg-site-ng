<?php

namespace Application\Controller\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Application\Controller\ImageController;
use Application\Service\ImageGeneratorCache;

class ImageControllerFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ) {
        $imageGenerator = $container->get(ImageGeneratorCache::class);

        return new ImageController($imageGenerator);
    }
}
