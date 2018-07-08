<?php

namespace Application\Controller\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Application\Controller\IndexController;
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
        $config = $config['idlerpg'];

        $parser = $container->get(BotParser::class);

        $imageGenerator = $container->get(ImageGenerator::class);

        $cache = $container->get('Cache');
        $cache->clearExpired();

        return new IndexController($config, $parser, $imageGenerator, $cache);
    }
}
