<?php
namespace Application\Service\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Application\Service\BotParser;
use Application\Service\BotParserCache;

class BotParserCacheFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ) {
        $parser = $container->get(BotParser::class);

        $cache = $container->get('Cache');
        $cache->clearExpired();

        return new BotParserCache($parser, $cache);
    }
}
