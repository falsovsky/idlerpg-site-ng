<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

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
                    'route'    => '/database.json',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'databaseJson',
                    ],
                ],
            ],

            'playerinfo' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/playerinfo/:nick',
                    'constraints' => [
                        //'nick' => '[\a]+'
                    ],
                    'defaults' => [
                        'controller'    => Controller\IndexController::class,
                        'action'        => 'playerInfo',
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
        ],
    ],

    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Controller\Factory\IdleControllerFactory::class,
        ],
    ],

    'service_manager' => [
        'factories' => [
            BotParser::class => Service\Factory\BotParserFactory::class,
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
