<?php
return array(
    'modules' => array(
        'Althingi',
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            '../../../config/test/{,*.}{global,local}.php',
        ),
        'module_paths' => array(
            'module',
            'vendor',
        ),
    ),

    'db' => [
        'dns' => 'mysql:host=127.0.0.1;dbname=althingi',
        'user' => 'root',
        'password' => '',
    ]
);
