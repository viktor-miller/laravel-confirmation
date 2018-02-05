<?php

namespace ViktorMiller\LaravelConfirmation;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Foundation\Application;
use ViktorMiller\LaravelConfirmation\Repository\DatabaseTokenRepository;
use ViktorMiller\LaravelConfirmation\Contracts\BrokerManager as BrokerManagerContract;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
class BrokerManager implements BrokerManagerContract
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The array of created "drivers".
     *
     * @var array
     */
    protected $brokers = [];
    
    /**
     * Create a new EmailBroker manager instance.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }
    
    /**
     * Attempt to get the broker from the local cache.
     *
     * @param  string  $name
     * @return EmailBrokerInterface
     */
    public function broker($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        return isset($this->brokers[$name])
                    ? $this->brokers[$name]
                    : $this->brokers[$name] = $this->resolve($name);
    }
    
    /**
     * Resolve the given broker.
     *
     * @param  string  $name
     * @return EmailBrokerInterface
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);
                
        if (! is_array($config)) {
            throw new InvalidArgumentException(
                "Email broker [{$name}] is not defined."
            );
        }
        
        if (! array_has($config, 'provider')) {
            throw new InvalidArgumentException("User provider is not defined.");
        }

        return new Broker(
            $this->createTokenRepository($config),
            $this->app['auth']->createUserProvider(array_get($config, 'provider'))
        );
    }
    
    /**
     * Create a token repository instance based on the given configuration.
     *
     * @param  array $config
     * @return \ViktorMiller\LaravelConfirmation\Contracts\TokenRepository
     */
    protected function createTokenRepository(array $config)
    {
        $key = $this->app['config']['app.key'];

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        $connection = isset($config['connection']) ? $config['connection'] : null;

        return new DatabaseTokenRepository(
            $this->app['db']->connection($connection),
            $this->app['hash'],
            $config['table'],
            $key,
            $config['expire']
        );
    }
    
    /**
     * Get the email broker configuration.
     *
     * @param  string  $name
     * @return array
     */
    protected function getConfig($name)
    {
        return $this->app['config']["confirmation.emails.{$name}"];
    }
    
    
    /**
     * Get the default email broker name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['confirmation.defaults.emails'];
    }

    /**
     * Set the default email broker name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['confirmation.defaults.emails'] = $name;
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->broker()->{$method}(...$parameters);
    }
}
