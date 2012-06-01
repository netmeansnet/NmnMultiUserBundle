<?php

namespace Nmn\MultiUserBundle\Tests\Unit\Manager;

use Nmn\MultiUserBundle\Tests\Unit\TestCase;
use Nmn\MultiUserBundle\Security\InteractiveLoginListener as Listener;

class InteractiveLoginListenerTest extends TestCase
{
    public function setUp()
    {
        $this->userDiscriminator = $this->getMockBuilder('Nmn\MultiUserBundle\Manager\UserDiscriminator')->disableOriginalConstructor()->getMock();
        $this->event = $this->getMockBuilder('Symfony\Component\Security\Http\Event\InteractiveLoginEvent')->disableOriginalConstructor()->getMock();
        $this->token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken')->disableOriginalConstructor()->getMock();
        $this->user = $this->getMockBuilder('FOS\UserBundle\Model\UserInterface')->disableOriginalConstructor()->getMock();
        $this->userInvalid = $this->getMockBuilder('InvalidUser')->disableOriginalConstructor()->getMock();
    }
    
    public function test_onSecurityInteractiveLogin_ok()
    {
        $this->event->expects($this->once())->method('getAuthenticationToken')->will($this->returnValue($this->token));
        $this->token->expects($this->once())->method('getUser')->will($this->returnValue($this->user));
        $this->userDiscriminator->expects($this->once())->method('setClass')->with($this->equalTo(get_class($this->user)), $this->equalTo(true));
        
        $listener = new Listener($this->userDiscriminator);
        $listener->onSecurityInteractiveLogin($this->event);
    }
    
    public function test_onSecurityInteractiveLogin_ko()
    {
        $this->event->expects($this->once())->method('getAuthenticationToken')->will($this->returnValue($this->token));
        $this->token->expects($this->once())->method('getUser')->will($this->returnValue($this->userInvalid));
        $this->userDiscriminator->expects($this->never())->method('setClass');
        
        $listener = new Listener($this->userDiscriminator);
        $listener->onSecurityInteractiveLogin($this->event);
    }
}