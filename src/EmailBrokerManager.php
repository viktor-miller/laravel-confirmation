<?php

namespace ViktorMiller\LaravelConfirmation;

use Illuminate\Support\Str;
use InvalidArgumentException;
use ViktorMiller\LaravelConfirmation\Repository\DatabaseTokenRepository;

/**
 * Description of EmailBrokerManager
 *
 * @author viktormiller
 */
class EmailBrokerManager
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
    public function __construct($app)
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

        if (is_null($config)) {
            throw new InvalidArgumentException("Email confirmer [{$name}] is not defined.");
        }

        return new EmailBroker(
            $this->createTokenRepository($config),
            $this->app['auth']->createUserProvider($config['provider'])
        );
    }
    
    /**
     * Create a token repository instance based on the given configuration.
     *
     * @param  array $config
     * @return \ViktorMiller\LaravelConfirmation\Repository\TokenRepositoryInterface
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
