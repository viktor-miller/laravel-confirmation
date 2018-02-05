<?php

namespace ViktorMiller\LaravelConfirmationTests;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ViktorMiller\LaravelConfirmation\Contracts\Broker;
use ViktorMiller\LaravelConfirmation\Facades\Confirmation;
use App\Notifications\Auth\Confirmation as ConfirmNotification;

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
    }  
    
    /**
     * Test whether a new token will be created and a notification will 
     * be sent to an unconfirmed user
     * 
     * @return void 
     */
    public function testSend()
    {   
        Notification::fake();
        
        $result = Confirmation::send($this->user);
        
        $this->assertEquals(Broker::CONFIRM_LINK_SENT, $result);
        
        Notification::assertSentTo($this->user, ConfirmNotification::class);
    }
    
    /**
     * Test whether a new token will be created and a notification will 
     * be sent to a confirmed user
     * 
     * @return void 
     */
    public function testSendToConfirmedUser()
    {
        $this->user->confirmed = true;
        $this->user->save();
        
        $result = Confirmation::send($this->user);
        
        $this->assertEquals(Broker::INVALID_USER, $result);
    }
    
    /**
     * Test whether a new token will be created and sent to unknown user
     * 
     * @return void 
     */
    public function testSendToUnknowUser()
    {
        $result = Confirmation::send(str_random(6) .'@mail.com');
        
        $this->assertEquals(Broker::INVALID_USER, $result);
    }
    
    /**
     * Test whether a new token will be created
     * 
     * @return void
     */
    public function testCreateToken()
    {
        $token = Confirmation::createToken($this->user);
        
        $this->assertTrue(is_string($token));
    }
    
    /**
     * Test if the received token exists
     * 
     * @return void
     */
    public function testExistsToken()
    {
        $token = Confirmation::createToken($this->user);
        
        $result = Confirmation::existsToken($this->user, $token);
        
        $this->assertTrue($result);
    }
    
    /**
     * Test whether an unconfirmed user will be confirmed
     * 
     * @return void
     */
    public function testConfirm()
    {   
        $token = Confirmation::createToken($this->user);
        
        $result = Confirmation::confirm($this->user, $token, function($user) {
            $user->confirmed = true;
            $user->save();
        });
        
        $this->assertEquals(Broker::EMAIL_CONFIRMED, $result);
        
        $this->user->refresh();
        
        $this->assertTrue((bool) $this->user->confirmed);
    }
    
    /**
     * Test whether an already verified user will be verified
     * 
     * @return void
     */
    public function testConfirmConfirmedUser()
    {   
        $this->user->confirmed = true;
        $this->user->save();
        
        $token = Confirmation::createToken($this->user);
        
        $result = Confirmation::confirm($this->user, $token, function() {});
        
        $this->assertEquals(Broker::INVALID_USER, $result);
    }
    
    /**
     * Test whether an already verified user will be verified
     * 
     * @return void
     */
    public function testConfirmUnknownUser()
    {
        $email = str_random(5). '@mail.com';
        $token = Confirmation::createToken($this->user);
        
        $result = Confirmation::confirm($email, $token, function(){});
        
        $this->assertEquals(Broker::INVALID_USER, $result);
    }
    
    
    /**
     * Test whether the unconfirmed user will be confirmed with 
     * an incorrect token
     * 
     * @return void
     */
    public function testConfirmWrongToken()
    {
        Confirmation::createToken($this->user);
        
        $result = Confirmation::confirm(
            $this->user, str_random(20), function(){}
        );
        
        $this->assertEquals(Broker::INVALID_TOKEN, $result);
    }
    
    /**
     * Test whether a new token will be deleted
     * 
     * @return void
     */
    public function testDeleteToken()
    {
        $token = Confirmation::createToken($this->user);
        
        Confirmation::deleteToken($this->user);
        
        $result = Confirmation::confirm($this->user, $token, function(){});
        
        $this->assertEquals(Broker::INVALID_TOKEN, $result);
    }
}
