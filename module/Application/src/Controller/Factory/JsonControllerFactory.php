<?php

namespace Application\Controller\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Application\Controller\JsonController;
use Application\Service\BotParserCache;

class JsonControllerFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ) {
        $parser = $container->get(BotParserCache::class);

        return new JsonController($parser);
    }
}
