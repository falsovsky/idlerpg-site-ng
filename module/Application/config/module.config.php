<?php

namespace Application;

use Zend\ServiceManager\Factory\InvokableFactory;
use Application\Service\BotParser;
use Application\Service\ImageGenerator;
use Application\View\Helper\Scoreboard;

return [
    'router' => require __DIR__ . '/router.config.php',
    'navigation' => require __DIR__ . '/navigation.config.php',

    'service_manager' => [
        'factories' => [
            'Cache' => Service\Factory\CacheFactory::class,
            ImageGenerator::class => Service\Factory\ImageGeneratorFactory::class,
            BotParser::class => Service\Factory\BotParserFactory::class,
        ],
    ],

    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Controller\Factory\IndexControllerFactory::class,
            Controller\JsonController::class => Controller\Factory\JsonControllerFactory::class,
            Controller\ImageController::class => Controller\Factory\ImageControllerFactory::class,
        ],
    ],

    'view_helpers' => [
        'aliases' => [
            'scoreboard' => Scoreboard::class,
        ],
        'factories' => [
            Scoreboard::class => InvokableFactory::class,
        ],
    ],

    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/game-info.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
];
