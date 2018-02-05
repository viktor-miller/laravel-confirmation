<?php

namespace ViktorMiller\LaravelConfirmationTests\Http\Controllers;

use Session;
use App\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
class SendConfirmationControllerTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * @var \ViktorMiller\LaravelConfirmation\Contracts\Confirmable
     */
    protected $user;
    
    /**
     * init user and broker
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->user = factory(User::class)->create();
    }  
    
    /**
     * We are testing the request to display the HTML form of the form
     * 
     * @return void
     */
    public function testIndex()
    {
        $response = $this->get(route('confirmation'));
        $response->assertStatus(200);
    }
    
    /**
     * We test the request to send a notification to confirm Email-Addresses
     * 
     * @return void
     */
    public function testSend()
    {     
        Session::start();
        Notification::fake();
         
        $response = $this->call('POST', route('confirmation.send'), [
            '_token' => csrf_token(),
            'email' => $this->user->email
        ]);

        $response
            ->assertStatus(302)
            ->assertSessionHas('success');
    }
    
    /**
     * We test the request to send a message to confirm Email-Address 
     * (User has already been verified)
     * 
     * @return void
     */
    public function testSendConfirmedUser()
    {   
        Session::start();
        Notification::fake();
    
        $this->user->confirmed = true;
        $this->user->save();
         
        $response = $this->call('POST', route('confirmation.send'), [
            '_token' => csrf_token(),
            'email' => $this->user->email
        ]);
        $response
            ->assertStatus(302)
            ->assertSessionHas('error');
    }
    
    /**
     * We test the request to send a message to confirm Email-Address 
     * (unknown User)
     * 
     * @return void
     */
    public function testSendUnknownUser()
    {   
        Session::start();
        Notification::fake();
    
        $this->user->confirmed = true;
        $this->user->save();
         
        $response = $this->call('POST', route('confirmation.send'), [
            '_token' => csrf_token(),
            'email' => str_random(30) .'@mail.com'
        ]);
        $response
            ->assertStatus(302)
            ->assertSessionHas('error');
    }
}
