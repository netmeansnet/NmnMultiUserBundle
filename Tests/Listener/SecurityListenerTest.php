<?php

namespace PUGX\MultiUserBundle\Tests\Controller;

use PUGX\MultiUserBundle\Listener\SecurityListener;
use PUGX\MultiUserBundle\Tests\Stub\User;

class SecurityListenerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->userDiscriminator = $this->getMockBuilder('PUGX\MultiUserBundle\Model\UserDiscriminator')
                ->disableOriginalConstructor()->getMock();    
        $this->interactiveLoginEvent = $this->getMockBuilder('Symfony\Component\Security\Http\Event\InteractiveLoginEvent')
                ->disableOriginalConstructor()->getMock();  
        $this->manualLoginEvent = $this->getMockBuilder('PUGX\MultiUserBundle\Event\ManualLoginEvent')
                ->disableOriginalConstructor()->getMock();                  
        $this->token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken')
                ->disableOriginalConstructor()->getMock();       
        $this->user = new User();  
        
        $this->listener = new SecurityListener($this->userDiscriminator);
    }
    
    public function testOnSecurityInteractiveLogin()
    {
        $this->interactiveLoginEvent->expects($this->once())->method('getAuthenticationToken')->will($this->returnValue($this->token));
        $this->token->expects($this->once())->method('getUser')->will($this->returnValue($this->user));        
        $this->userDiscriminator->expects($this->exactly(1))->method('setClass')->with('PUGX\MultiUserBundle\Tests\Stub\User', true);
        
        $this->listener->onSecurityInteractiveLogin($this->interactiveLoginEvent);       
    }
    
    public function testOnSecurityManualLogin()
    {
        $this->manualLoginEvent->expects($this->once())->method('getUser')->will($this->returnValue($this->user));        
        $this->userDiscriminator->expects($this->exactly(1))->method('setClass')->with('PUGX\MultiUserBundle\Tests\Stub\User', true);
        
        $this->listener->onSecurityManualLogin($this->manualLoginEvent);         
    }
}