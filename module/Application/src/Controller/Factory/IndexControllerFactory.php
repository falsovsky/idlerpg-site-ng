<?php

namespace Application\Controller\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Application\Controller\IndexController;
use Application\Service\BotParserCache;

class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ) {
        $config = $container->get('configuration');
        $config = $config['idlerpg'];

        $parser = $container->get(BotParserCache::class);

        return new IndexController($config, $parser);
    }
}
