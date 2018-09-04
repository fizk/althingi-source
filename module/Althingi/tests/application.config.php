<?php

return [
    'modules' => [
        'Althingi',
    ],
    'module_listener_options' => [
        'config_glob_paths'    => [
            '../../../config/test/{,*.}{global,local}.php',
        ],
        'module_paths' => [
            'module',
            'vendor',
        ],
    ],
];
