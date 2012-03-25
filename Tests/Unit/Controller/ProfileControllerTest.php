<?php

namespace Nmn\UserBundle\Tests\Unit\Controller;

use Nmn\UserBundle\Tests\Unit\TestCase;
use Nmn\UserBundle\Controller\ProfileController;

class ProfileControllerTest extends TestCase
{
    public function setUp()
    {
        $this->container            = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')->disableOriginalConstructor()->getMock();
        $this->userDiscriminator    = $this->getMockBuilder('Nmn\UserBundle\Manager\UserDiscriminator')->disableOriginalConstructor()->getMock();
        $this->securityContext      = $this->getMock('SecurityContext', array('getToken'));
        $this->token                = $this->getMock('Token', array('getUser'));
    }
    
    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException 
     */
    public function testRegisterAction()
    {
        $controller = new ProfileController();
        $controller->setContainer($this->container);
        
        $this->container->expects($this->exactly(2))->method('get')->with($this->logicalOr(
                'nmn_user_discriminator',
                'security.context'))
                ->will($this->onConsecutiveCalls($this->userDiscriminator, $this->securityContext));         
        
        $this->userDiscriminator->expects($this->exactly(1))->method('getProfileForm')->will($this->onConsecutiveCalls('form')); 
        $this->container->expects($this->exactly(1))->method('set')->with('fos_user.profile.form', 'form'); 
        $this->securityContext->expects($this->exactly(1))->method('getToken')->will($this->onConsecutiveCalls($this->token)); 
        $this->token->expects($this->exactly(1))->method('getUser')->will($this->onConsecutiveCalls(null)); 
                
        $response = $controller->editAction();
    }
}