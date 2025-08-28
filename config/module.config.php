<?php

namespace FixCliAndJob;

return [
    'service_manager' => [
        'factories' => [
            'Omeka\Cli' => Service\CliFactory::class
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\Admin\PerformJobController::class => Service\Controller\Admin\PerformJobControllerFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'admin' => [
                'child_routes' => [
                    'fix-cli-and-job-testing-loop' => [
                        'type' => \Laminas\Router\Http\Segment::class,
                        'options' => [
                            'route' => '/testing-loop-job[/:loop][/:timeout]',
                            'constraints' => [
                                'loop' => '\d+',
                                'timeout' => '\d+'
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'FixCliAndJob\Controller\Admin',
                                'controller' => Controller\Admin\PerformJobController::class,
                                'action' => 'testingloop'
                            ],
                        ],
                    ],
                ],
            ],
            'fix-cli-and-job-perform-job' => [
                'type' => \Laminas\Router\Http\Segment::class,
                'options' => [
                    'route' => '/perform-job/:id',
                    'constraints' => [
                        'id' => '\d+'
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'FixCliAndJob\Controller\Admin',
                        'controller' => Controller\Admin\PerformJobController::class,
                        'action' => 'execute'
                    ],
                ],
            ],
            'fix-cli-and-job-perform-jobs' => [
                'type' => \Laminas\Router\Http\Segment::class,
                'options' => [
                    'route' => '/perform-jobs',
                    'defaults' => [
                        '__NAMESPACE__' => 'FixCliAndJob\Controller\Admin',
                        'controller' => Controller\Admin\PerformJobController::class,
                        'action' => 'executing'
                    ],
                ],
            ],
        ],
    ],
    'FixCliAndJob' => [
        'config' => [
            'executeJob' => 'execute',
            'time_limit' => 600,
            'CRON_Jobs_limit' => 3,
            'path_permissions' => dirname(__DIR__).'/data/permissions',
        ]
    ]
];
