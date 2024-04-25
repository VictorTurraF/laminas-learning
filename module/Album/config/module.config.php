<?php

namespace Album;

use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\Router\Http\Segment;
use Album\Model\AlbumTableFactory;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;

return [
    'controllers' => [
        'factories' => [
            Controller\AlbumController::class => ReflectionBasedAbstractFactory::class,
        ],
    ],

    'router' => [
        'routes' => [
            'album' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/album[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\AlbumController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],

    'service_manager' => [
        'factories' => [
            Model\AlbumTable::class => AlbumTableFactory::class,
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            'album' => __DIR__ . '/../view',
        ],
    ],
];
