<?php

namespace Application;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;
use Application\Service\BotParser;
use Application\View\Helper\Scoreboard;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'gameInfo',
                    ],
                ],
            ],

            'scoreboard' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/scoreboard',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'scoreBoard',
                    ],
                ],
            ],

            'database' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/database',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'database',
                    ],
                ],
            ],

            'database-json' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/api/database',
                    'defaults' => [
                        'controller' => Controller\JsonController::class,
                        'action'     => 'database',
                    ],
                ],
            ],

            'player-info' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/playerinfo[:mod]/:nick',
                    'constraints' => [
                        'mod' => '-full-modifier',
                    ],
                    'defaults' => [
                        'controller'    => Controller\IndexController::class,
                        'action'        => 'playerInfo',
                    ],
                ],
            ],

            'player-map-image' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/images/playermap/:nick',
                    'defaults' => [
                        'controller'    => Controller\ImageController::class,
                        'action'        => 'playerMap',
                    ],
                ],
            ],

            'world-map' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/worldmap',
                    'defaults' => [
                        'controller'    => Controller\IndexController::class,
                        'action'        => 'worldMap',
                    ],
                ],
            ],

            'world-map-image' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/images/worldmap',
                    'defaults' => [
                        'controller'    => Controller\ImageController::class,
                        'action'        => 'worldMap',
                    ],
                ],
            ],

        ],
    ],

    'navigation' => [
        'default' => [
            [
                'label' => 'Game Info',
                'route' => 'home',
            ],
            [
                'label' => 'Scoreboard',
                'route' => 'scoreboard',
            ],
            [
                'label' => 'Player database',
                'route' => 'database',
            ],
            [
                'label' => 'World Map',
                'route' => 'world-map',
            ],
        ],
    ],

    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Controller\Factory\IdleControllerFactory::class,
            Controller\JsonController::class => Controller\Factory\IdleControllerFactory::class,
            Controller\ImageController::class => Controller\Factory\IdleControllerFactory::class,
        ],
    ],

    'service_manager' => [
        'factories' => [
            BotParser::class => Service\Factory\BotParserFactory::class,
            'Cache' => Service\Factory\CacheFactory::class,
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
