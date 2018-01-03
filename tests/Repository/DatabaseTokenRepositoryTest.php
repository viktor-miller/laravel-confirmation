<?php

namespace ViktorMiller\LaravelConfirmationTests;

use StdClass;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ViktorMiller\LaravelConfirmation\Repository\DatabaseTokenRepository;
use ViktorMiller\LaravelConfirmation\Repository\TokenRepositoryInterface;

/**
 *
 * @author viktormiller
 */
class DatabaseRepositoryTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * @var TokenRepositoryInterface 
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
     * Test if a record is created
     */
    public function testCreate()
    {   
        $token = $this->repository->create($this->user);
        
        $this->assertTrue(is_string($token));
        $this->assertTrue(strlen($token) > 0);
    }
    
    /**
     * Test if a record is found by token
     */
    public function testRetriveByToken()
    {
        $token = $this->repository->create($this->user);
        
        $this->assertInstanceOf(StdClass::class, 
                $this->repository->retriveByToken($token));
    }
    
    /**
     * Test if a record is found by user
     */
    public function testRetriveByUser()
    {
        $this->repository->create($this->user);
        
        $this->assertInstanceOf(StdClass::class, 
                $this->repository->retriveByUser($this->user));
    }
    
    /**
     * Test if a record is deleted
     */
    public function testDelete()
    {
        $this->repository->create($this->user);
        
        $this->assertTrue(is_int($this->repository->delete($this->user)));
    }
    
    /**
     * Test if expired records are deleted
     */
    public function testDeleteExpired()
    {
        $this->assertTrue(is_int($this->repository->deleteExpired()));
    }
}
