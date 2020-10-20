<?php

namespace Application;

use Laminas\Mvc\MvcEvent;

class Module
{
    const VERSION = '3.0.3-dev';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $event)
    {
        $serviceManager = $event->getApplication()->getServiceManager();
        $viewModel = $event->getApplication()->getMvcEvent()->getViewModel();

        $config = $serviceManager->get('configuration');
        $config = $config['idlerpg'];

        $viewModel->title = $config['bot_channel'] . '[at]' . $config['network_host'];
    }
}
