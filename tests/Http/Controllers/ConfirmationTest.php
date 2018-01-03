<?php

namespace ViktorMiller\LaravelConfirmationTests\Http\Controllers;

use Session;
use App\User;
use Tests\TestCase;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
/**
 *
 * @author viktormiller
 */
class ConfirmationTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * @var \ViktorMiller\LaravelConfirmation\ShouldConfirmEmailInterface
     */
    protected $user;
    
    /**
     * @var \ViktorMiller\LaravelConfirmation\EmailBrokerInterface
     */
    protected $broker;
    
    /**
     * init user and broker
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->user = factory(User::class)->create();
        $this->broker = $this->app['confirmation.email.broker'];
        
        Session::start();
        Notification::fake();
    }  
    
    /**
     * Test if http code is 200
     */
    public function testIndex()
    {
        $response = $this->get('/confirmation');

        $response->assertStatus(200);
    }
    
    /**
     * Test if confirm link was sent to unconfirmed user
     */
    public function testSend()
    {
        $credentials = [
            '_token' => csrf_token(),
            'email' => $this->user->email
        ];
         
        $response = $this->call('POST', '/confirmation', $credentials);

        $response
            ->assertStatus(302)
            ->assertSessionHas('success');
    }
    
    /**
     * Test if confirm link wasn't sent to confirmed user
     */
    public function testSendConfirmed()
    {   
        $this->user->confirmed = true;
        $this->user->save();
        
        $credentials = [
            '_token' => csrf_token(),
            'email' => $this->user->email
        ];
         
        $response = $this->call('POST', '/confirmation', $credentials);

        $response
            ->assertStatus(302)
            ->assertSessionHas('error');
    }
    
    /**
     * Test if user was confirmed by valid token.
     */
    public function testConfirm()
    {   
        event(new Registered($this->user));
        
        $token = $this->broker->getToken($this->user);
        
        $response = $this->call('GET', route('confirmation', $token));
        
        $response
            ->assertStatus(302)
            ->assertSessionHas('success');
    }
    
    /**
     * Test if confirmed user wasn't confirmed by valid token
     */
    public function testConfirmConfirmed()
    {   
        event(new Registered($this->user));
        
        $this->user->confirmed = true;
        $this->user->save();
        
        $token = $this->broker->getToken($this->user);
        
        $response = $this->call('GET', route('confirmation', $token));
        
        $response
            ->assertStatus(302)
            ->assertSessionHas('error');
    }
    
    /**
     * Test if user was confirmed by invalid token
     */
    public function testConfirmInvalidToken()
    {
        event(new Registered($this->user));
        
        $response = $this->call('GET', route('confirmation', 'abcde'));
        
        $response
            ->assertStatus(302)
            ->assertSessionHas('error');
    }
}
