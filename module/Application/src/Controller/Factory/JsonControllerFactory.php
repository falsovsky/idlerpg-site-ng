<?php

namespace Application\Controller\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Application\Controller\JsonController;
use Application\Service\BotParser;

class JsonControllerFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ) {
        $parser = $container->get(BotParser::class);

        $cache = $container->get('Cache');
        $cache->clearExpired();

        return new JsonController($parser, $cache);
    }
}
