<?php

namespace ViktorMiller\LaravelConfirmation\Contracts;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
interface BrokerManager
{
    /**
     * 
     * @param  string $name
     * @return Broker
     */
    public function broker($name = null);
}
