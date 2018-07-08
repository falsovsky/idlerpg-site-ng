<?php

namespace Application\Controller\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Application\Service\BotParser;
use Application\Service\ImageGenerator;

class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ) {
        $config = $container->get('configuration');
        $idlerpg = $config['idlerpg'];

        $parser = $container->get(BotParser::class);

        $imageGenerator = $container->get(ImageGenerator::class);

        return new $requestedName($idlerpg, $parser, $imageGenerator);
    }
}
