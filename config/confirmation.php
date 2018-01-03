<?php

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */

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

