<?php

namespace ViktorMiller\LaravelConfirmation\Facades;

use Illuminate\Support\Facades\Facade;
use ViktorMiller\LaravelConfirmation\Contracts\BrokerManager;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
class Confirmation extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BrokerManager::class;
    }
}
