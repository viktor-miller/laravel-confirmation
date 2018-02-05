<?php

namespace ViktorMiller\LaravelConfirmation\Http\Controllers;

use ViktorMiller\LaravelConfirmation\Facades\Confirmation;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
trait ConfirmationBroker
{
    /**
     * 
     * @return \ViktorMiller\LaravelConfirmation\Contracts\Broker
     */
    protected function broker()
    {
        return Confirmation::broker();
    }
}
