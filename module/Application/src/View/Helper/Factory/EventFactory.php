<?php
namespace Application\View\Helper\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Application\Service\BotParser;
use Application\View\Helper\Event;

class EventFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ) {
        $parser = $container->get(BotParser::class);

        return new Event($parser->getPlayers());
    }
}
