<?php

namespace Application;

use Laminas\ServiceManager\Factory\InvokableFactory;
use Application\Service\BotParser;
use Application\Service\BotParserCache;
use Application\View\Helper\Scoreboard;
use Application\View\Helper\PlayerItem;
use Application\View\Helper\Event;

return [
    'router' => require __DIR__ . '/router.config.php',
    'navigation' => require __DIR__ . '/navigation.config.php',

    'service_manager' => [
        'factories' => [
            'Cache' => Service\Factory\CacheFactory::class,
            BotParserCache::class => Service\Factory\BotParserCacheFactory::class,
            BotParser::class => Service\Factory\BotParserFactory::class,
        ],
    ],

    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Controller\Factory\IndexControllerFactory::class,
            Controller\JsonController::class => Controller\Factory\JsonControllerFactory::class,
        ],
    ],

    'view_helpers' => [
        'aliases' => [
            'scoreboard' => Scoreboard::class,
            'playeritem' => PlayerItem::class,
            'event'      => Event::class,
        ],
        'factories' => [
            Scoreboard::class => InvokableFactory::class,
            PlayerItem::class => InvokableFactory::class,
            Event::class       => View\Helper\Factory\EventFactory::class,
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
