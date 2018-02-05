<?php

namespace ViktorMiller\LaravelConfirmationTests\Http\Controllers;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ViktorMiller\LaravelConfirmation\Facades\Confirmation;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
class ConfirmEmailTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * @var \ViktorMiller\LaravelConfirmation\ShouldConfirmEmailInterface
     */
    protected $user;
    
    /**
     * @var \ViktorMiller\LaravelConfirmation\Contracts\Broker
     */
    protected $broker;
    
    /**
     * init user and broker
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->user = factory(User::class)->create();
        $this->broker = Confirmation::broker();
    }  
    
    /**
     * We test the request to display the HTML form
     */
    public function testIndex()
    {
        $response = $this->call('get', route('confirmation.manual'));
        $response->assertStatus(200);
    }
    
    /**
     * We are testing the request for the verification of the email address
     * 
     * @return void 
     */
    public function testConfirm()
    {   
        $token = $this->broker->createToken($this->user);
        
        $response = $this->call('get', route('confirmation.auto'), [
            'email' => $this->user->email,
            'token'=> $token
        ]);
        
        $response
            ->assertStatus(302)
            ->assertSessionHas('success');
    }
    
    /**
     * We test the request for the confirmation of the email address 
     * (User has already been verified)
     * 
     * @return void
     */
    public function testConfirmConfirmed()
    {   
        $token = $this->broker->createToken($this->user);
        
        $this->user->confirmed = true;
        $this->user->save();
        
        $response = $this->call('GET', route('confirmation.auto'), [
            'email' => $this->user->email,
            'token' => $token
        ]);
        
        $response
            ->assertStatus(302)
            ->assertSessionHas('error');
    }
    
    /**
     * We test the request for the confirmation of the email address 
     * (Invalid token)
     * 
     * @return void
     */
    public function testConfirmInvalidToken()
    {
        $this->broker->createToken($this->user);
        
        $response = $this->call('GET', route('confirmation.auto'), [
            'email' => $this->user->email,
            'token' => str_random(10)
        ]);
        
        $response
            ->assertStatus(302)
            ->assertSessionHas('error');
    }
}
