<?php

namespace PUGX\MultiUserBundle\Tests\Controller;

use PUGX\MultiUserBundle\Controller\RegistrationManager;

class RegistrationManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->discriminator = $this->getMockBuilder('PUGX\MultiUserBundle\Model\UserDiscriminator')
                ->disableOriginalConstructor()->getMock();
        
        $this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
                ->disableOriginalConstructor()->getMock();
        
        $this->controller = $this->getMockBuilder('FOS\UserBundle\Controller\RegistrationController')
                ->disableOriginalConstructor()->getMock();
        
        $this->formFactory = $this->getMockBuilder('PUGX\MultiUserBundle\Form\FormFactory')
                ->disableOriginalConstructor()->getMock();
        
        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
                ->disableOriginalConstructor()->getMock();
        
        $this->redirectResponse = $this->getMockBuilder('Symfony\Component\HttpFoundation\RedirectResponse')
                ->disableOriginalConstructor()->getMock();
        
        $this->form = $this->getMockBuilder('Symfony\Component\Form\Form')
                ->disableOriginalConstructor()->getMock();
        
        $this->twig = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface')
                ->disableOriginalConstructor()->getMock();
        
        $this->formView = $this->getMockBuilder('Symfony\Component\Form\FormView')
                ->disableOriginalConstructor()->getMock();
        
        $this->userManager = new RegistrationManager($this->discriminator, $this->container, $this->controller, $this->formFactory);
    }
    
    public function common()
    {
        $this->discriminator
                ->expects($this->exactly(1))
                ->method('setClass')
                ->with('MyUser');
        
        $this->controller
                ->expects($this->exactly(1))
                ->method('setContainer')
                ->with($this->container);
        
        $this->container
                ->expects($this->at(0))
                ->method('get')
                ->with('request')
                ->will($this->returnValue($this->request));
    }


    public function testRegisterReturnRedirectResponse()
    {
        $this->common();
        
        $this->controller
                ->expects($this->exactly(1))
                ->method('registerAction')
                ->with($this->request)
                ->will($this->returnValue($this->redirectResponse));
        
        $result = $this->userManager->register('MyUser');
        
        $this->assertSame($result, $this->redirectResponse);
    }
    
    public function testRegisterReturnDefaultTemplate()
    {
        $this->common();
        
        $this->controller
                ->expects($this->exactly(1))
                ->method('registerAction')
                ->with($this->request)
                ->will($this->returnValue(null));
        
        $this->discriminator
                ->expects($this->exactly(1))
                ->method('getTemplate')
                ->with('registration')
                ->will($this->returnValue(null));
        
        $this->container
                ->expects($this->at(1))
                ->method('getParameter')
                ->with('fos_user.template.engine')
                ->will($this->returnValue('twig'));
        
        $this->formFactory
                ->expects($this->exactly(1))
                ->method('createForm')
                ->will($this->returnValue($this->form));
        
        $this->container
                ->expects($this->at(2))
                ->method('get')
                ->with('templating')
                ->will($this->returnValue($this->twig));
        
        $this->twig
                ->expects($this->exactly(1))
                ->method('renderResponse')
                ->with('FOSUserBundle:Registration:register.html.twig', array('form' => $this->formView));
        
        $this->form
                ->expects($this->exactly(1))
                ->method('createView')
                ->will($this->returnValue($this->formView));
                
        $result = $this->userManager->register('MyUser');
    }
    
    public function testRegisterReturnSpecificTemplate()
    {
        $this->common();
        
        $this->controller
                ->expects($this->exactly(1))
                ->method('registerAction')
                ->with($this->request)
                ->will($this->returnValue(null));
        
        $this->discriminator
                ->expects($this->exactly(1))
                ->method('getTemplate')
                ->with('registration')
                ->will($this->returnValue('PUGXMultiUserBundle:Registration:register.html.twig'));
        
        $this->formFactory
                ->expects($this->exactly(1))
                ->method('createForm')
                ->will($this->returnValue($this->form));
        
        $this->container
                ->expects($this->at(1))
                ->method('get')
                ->with('templating')
                ->will($this->returnValue($this->twig));
        
        $this->twig
                ->expects($this->exactly(1))
                ->method('renderResponse')
                ->with('PUGXMultiUserBundle:Registration:register.html.twig', array('form' => $this->formView));
        
        $this->form
                ->expects($this->exactly(1))
                ->method('createView')
                ->will($this->returnValue($this->formView));
                
        $result = $this->userManager->register('MyUser');
    }
}