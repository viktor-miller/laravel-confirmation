<?php

namespace ViktorMiller\LaravelConfirmationTests;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ViktorMiller\LaravelConfirmation\EmailBrokerInterface;
use ViktorMiller\LaravelConfirmation\Notifications\Confirmation;

/**
 * 
 * @package  laravel-confirmation
 * @author   Viktor Miller <phpfriq@gmail.com>
 */
class EmailBrokerTest extends TestCase
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
        
        Notification::fake();
    }  
    
    /**
     * Testen ob einem noch nicht bestetigten Benutzer Bestätigungslink gesendet
     * wird.
     */
    public function testSend()
    {   
        // exists unconfirmed user
        $credentials = [
            'email' => $this->user->email
        ];
        
        $this->assertEquals(EmailBrokerInterface::CONFIRM_LINK_SENT, 
            $this->broker->send($credentials));
        
        Notification::assertSentTo($this->user, Confirmation::class);
        
        // exists confirmed user
        $this->user->confirmed = true;
        $this->user->save();
        
        $this->assertEquals(EmailBrokerInterface::INVALID_USER, 
            $this->broker->send($credentials));
        
        // not exists user
        $this->assertEquals(EmailBrokerInterface::INVALID_USER, 
                $this->broker->send([
            'email' => 'test@mail123.com'
        ]));
    }
    
    /**
     * Test getToken method
     */
    public function testGetToken()
    {
        $this->broker->send([
            'email' => $this->user->email
        ]);
        
        $token = $this->broker->getToken($this->user);
        
        $this->assertTrue(is_string($token));
        $this->assertTrue(strlen($token) > 0);
    }
    
    /**
     * Test if unconfirmed user is confirmed by valid token
     */
    public function testConfirm()
    {   
        $this->broker->send([
            'email' => $this->user->email
        ]);
        
        $this->assertEquals(
            EmailBrokerInterface::EMAIL_CONFIRMED, $this->broker->confirm(
                $this->broker->getToken($this->user), function($user) {
                    $user->confirmed = true;
                    $user->save();
                }
            )
        );
        
        $this->user->refresh();
        
        $this->assertTrue((bool) $this->user->confirmed);
    }
    
    /**
     * Test if confirmed user is not confirmed by valid token
     */
    public function testConfirmConfirmed()
    {
        $this->broker->send([
            'email' => $this->user->email
        ]);
        
        $this->user->confirmed = true;
        $this->user->save();
        
        $this->assertEquals(
            EmailBrokerInterface::INVALID_USER, $this->broker->confirm(
                $this->broker->getToken($this->user), function() {})
        );
            
        $this->assertNull($this->broker->getToken($this->user));
    }
    
    /**
     * Test if unconfirmed user is not confirmed by invalid token
     */
    public function testConfirmWrongToken()
    {
        $this->broker->send([
            'email' => $this->user->email
        ]);
        
        $this->assertEquals(
            EmailBrokerInterface::INVALID_TOKEN, $this->broker->confirm(
                'abcdefghijklmnopqrstuvwxyz', function() {}
            )
        );
    }
}
