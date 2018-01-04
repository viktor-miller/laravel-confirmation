<?php

namespace ViktorMiller\LaravelConfirmation\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
class Email extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'confirmation.email';
    }
}
