<?php

return [
    'defaults' => [
        'emails' => 'users'
    ],
    
    'emails' => [
        'users' => [
            'provider' => 'users',
            'table' => 'email_confirmations',
            'expire' => 60
        ]
    ]
];

