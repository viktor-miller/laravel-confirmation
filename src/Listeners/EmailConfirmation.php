<?php

namespace ViktorMiller\LaravelConfirmation\Listeners;

use ViktorMiller\LaravelConfirmation\Contracts\Broker;
use ViktorMiller\LaravelConfirmation\Contracts\Confirmable;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
class EmailConfirmation
{
    /**
     * @var Broker 
     */
    protected $broker;
    
    /**
     * 
     * @param Broker $broker
     */
    public function __construct(Broker $broker)
    {
        $this->broker = $broker;
    }
    
    /**
     * Send confirmation notification to given user
     * 
     * @param  mixed $event
     * @return void
     */
    public function handle($event)
    {
        if ($event->user && $event->user instanceof Confirmable) {
            $this->broker->send($event->user);
        }
    }
}
