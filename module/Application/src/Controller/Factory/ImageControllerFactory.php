<?php

namespace Application\Controller\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Application\Service\ImageGenerator;

class ImageControllerFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ) {
        $imageGenerator = $container->get(ImageGenerator::class);

        return new $requestedName($imageGenerator);
    }
}