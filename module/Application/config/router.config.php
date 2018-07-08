<?php

namespace Application;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

return [
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
                    'mod' => '-full-events',
                    'nick' => '[a-zA-Z0-9_.-]+',
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
                'constraints' => [
                    'nick' => '[a-zA-Z0-9_.-]+',
                ],
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

        'quest-info' => [
            'type'    => Segment::class,
            'options' => [
                'route'    => '/quest',
                'defaults' => [
                    'controller'    => Controller\IndexController::class,
                    'action'        => 'questInfo',
                ],
            ],
        ],

        'quest-map-image' => [
            'type'    => Segment::class,
            'options' => [
                'route'    => '/images/quest',
                'defaults' => [
                    'controller'    => Controller\ImageController::class,
                    'action'        => 'questMap',
                ],
            ],
        ],

        'recent-events' => [
            'type'    => Segment::class,
            'options' => [
                'route'    => '/recent',
                'defaults' => [
                    'controller'    => Controller\IndexController::class,
                    'action'        => 'recentEvents',
                ],
            ],
        ],

    ],
];
