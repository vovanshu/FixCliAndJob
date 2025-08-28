<?php

return [
    'classes' => [
        'fixcliandjob' => 'Fix Cli&Job'
    ],
    'permissions' => [
        'fixcliandjob' => [
            'FixCliAndJob\Controller\Admin\PerformJobController' => [
                'Perform Job' => [
                    'execute', 'executing'
                ],
                'Test Loop Job' => [
                    'testingloop'
                ],
            ]
        ]
    ]
];
