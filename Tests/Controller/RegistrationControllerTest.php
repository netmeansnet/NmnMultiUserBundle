<?php

namespace PUGX\MultiUserBundle\Tests\Controller;

use PUGX\MultiUserBundle\Tests\TestCase;
use PUGX\MultiUserBundle\Controller\RegistrationController;

class RegistrationControllerTest extends TestCase
{
    public function setUp()
    {
        $this->container            = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')->disableOriginalConstructor()->getMock();
        $this->userDiscriminator    = $this->getMockBuilder('PUGX\MultiUserBundle\Manager\UserDiscriminator')->disableOriginalConstructor()->getMock();
        $this->formHandler          = $this->getMockBuilder('PUGX\MultiUserBundle\Form\Handler\RegistrationFormHandler')->disableOriginalConstructor()->getMock();
        $this->templating           = $this->getMock('TemplateEngine', array('renderResponse'));
        $this->form                 = $this->getMock('Form', array('createView'));
        $this->responseExpected     = $this->getMockBuilder('Symfony\Component\HttpFoundation\Response')->disableOriginalConstructor()->getMock();
    }
    
    public function testRegisterAction()
    {
        $controller = new RegistrationController();
        $controller->setContainer($this->container);
        
        $this->container->expects($this->exactly(4))->method('get')->with($this->logicalOr(
                'pugx_user_discriminator',
                'fos_user.registration.form',
                'fos_user.registration.form.handler',
                'templating'))
                ->will($this->onConsecutiveCalls($this->userDiscriminator, $this->form, $this->formHandler, $this->templating));         
        
        $this->userDiscriminator->expects($this->exactly(1))->method('getRegistrationForm')->will($this->onConsecutiveCalls('form')); 
        $this->container->expects($this->exactly(1))->method('set')->with('fos_user.registration.form', 'form')->will($this->onConsecutiveCalls(null)); 
        
        $this->container->expects($this->exactly(3))->method('getParameter')->with($this->logicalOr(
                'fos_user.registration.confirmation.enabled',
                'fos_user.template.theme',
                'fos_user.template.engine'))
                ->will($this->onConsecutiveCalls(false, 'theme', 'engine'));         
                 
        $this->formHandler->expects($this->exactly(1))->method('process')->will($this->onConsecutiveCalls(false));      
        $this->templating->expects($this->exactly(1))->method('renderResponse')->will($this->onConsecutiveCalls($this->responseExpected)); 
        $this->form->expects($this->exactly(1))->method('createView')->will($this->onConsecutiveCalls(null)); 
        
        $response = $controller->registerAction();
        
        $this->assertSame($this->responseExpected, $response);
    }
}