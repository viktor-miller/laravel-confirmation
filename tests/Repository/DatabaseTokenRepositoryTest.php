<?php

namespace ViktorMiller\LaravelConfirmationTests;

use StdClass;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ViktorMiller\LaravelConfirmation\Contracts\TokenRepository;
use ViktorMiller\LaravelConfirmation\Repository\DatabaseTokenRepository;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
class DatabaseTokenRepositoryTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * @var TokenRepository 
     */
    protected $repository;
    
    /**
     * @var User 
     */
    protected $user;
    
    /**
     * init repository and user
     */
    public function setUp()
    {
        parent::setUp();
        
        $name = $this->app['config']['confirmation.defaults.emails'];
        
        $this->repository = new DatabaseTokenRepository(
            $this->app['db']->connection(),
            $this->app['hash'],
            $this->app['config']["confirmation.emails.{$name}.table"],
            $this->app['config']['app.key'],
            $this->app['config']["confirmation.emails.{$name}.expire"]
        );
            
        $this->user = factory(User::class)->create();
    }
    
    /**
     * Test whether a new record will be created
     * 
     * @return void
     */
    public function testCreate()
    {   
        $token = $this->repository->create($this->user);
        
        $this->assertTrue(is_string($token));
        $this->assertTrue(strlen($token) > 0);
    }
    
    /**
     * Check if a record is found
     * 
     * @return void
     */
    public function testExists()
    {   
        $token = $this->repository->create($this->user);
        
        $result = $this->repository->exists($this->user, $token);
        
        $this->assertTrue($result);
    }
    
    /**
     * Check if the entry is deleted
     * 
     * @return void
     */
    public function testDelete()
    {
        $this->repository->create($this->user);
        
        $result = $this->repository->delete($this->user);
        
        $this->assertTrue(is_int($result));
    }
    
    /**
     * Check if expired entries are deleted
     * 
     * @return void
     */
    public function testDeleteExpired()
    {
        $result = $this->repository->deleteExpired();
        
        $this->assertTrue(is_int($result));
    }
}
