<?php

namespace Application\Controller\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Application\Controller\ImageController;
use Application\Service\ImageGenerator;

class ImageControllerFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ) {
        $imageGenerator = $container->get(ImageGenerator::class);

        $cache = $container->get('Cache');
        $cache->clearExpired();

        return new ImageController($imageGenerator, $cache);
    }
}
