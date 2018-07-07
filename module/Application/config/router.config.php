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
];
