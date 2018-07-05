<?php
namespace Application\Service\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Application\Service\BotParser;

// Factory class
class BotParserFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container,
                             $requestedName, array $options = null)
    {
        $config = $container->get('configuration');
        $idlerpg = $config['idlerpg'];

        return new Botparser($idlerpg);
    }
}

