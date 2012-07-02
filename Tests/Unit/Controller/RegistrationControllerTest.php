<?php

namespace Nmn\MultiUserBundle\Tests\Unit\Controller;

use Nmn\MultiUserBundle\Tests\Unit\TestCase;
use Nmn\MultiUserBundle\Controller\RegistrationController;

class RegistrationControllerTest extends TestCase
{
    public function setUp()
    {
        $this->container            = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')->disableOriginalConstructor()->getMock();
        $this->userDiscriminator    = $this->getMockBuilder('Nmn\MultiUserBundle\Manager\UserDiscriminator')->disableOriginalConstructor()->getMock();
        $this->formHandler          = $this->getMockBuilder('Nmn\MultiUserBundle\Form\Handler\RegistrationFormHandler')->disableOriginalConstructor()->getMock();
        $this->templating           = $this->getMock('TemplateEngine', array('renderResponse'));
        $this->form                 = $this->getMock('Form', array('createView'));
        $this->responseExpected     = $this->getMockBuilder('Symfony\Component\HttpFoundation\Response')->disableOriginalConstructor()->getMock();
    }
    
    public function testRegisterAction()
    {
        $controller = new RegistrationController();
        $controller->setContainer($this->container);
        
        $this->container->expects($this->exactly(4))->method('get')->with($this->logicalOr(
                'nmn_user_discriminator',
                'fos_user.registration.form',
                'fos_user.registration.form.handler',
                'templating'))
                ->will($this->onConsecutiveCalls($this->userDiscriminator, $this->form, $this->formHandler, $this->templating));         
        
        $this->userDiscriminator->expects($this->exactly(1))->method('getRegistrationForm')->will($this->onConsecutiveCalls('form')); 
        $this->container->expects($this->exactly(1))->method('set')->with('fos_user.registration.form', 'form')->will($this->onConsecutiveCalls(null)); 
        
        $this->container->expects($this->exactly(2))->method('getParameter')->with($this->logicalOr(
                'fos_user.registration.confirmation.enabled',
                'fos_user.template.engine'))
                ->will($this->onConsecutiveCalls(false, 'engine'));
                 
        $this->formHandler->expects($this->exactly(1))->method('process')->will($this->onConsecutiveCalls(false));      
        $this->templating->expects($this->exactly(1))->method('renderResponse')->will($this->onConsecutiveCalls($this->responseExpected)); 
        $this->form->expects($this->exactly(1))->method('createView')->will($this->onConsecutiveCalls(null)); 
        
        $response = $controller->registerAction();
        
        $this->assertSame($this->responseExpected, $response);
    }
}