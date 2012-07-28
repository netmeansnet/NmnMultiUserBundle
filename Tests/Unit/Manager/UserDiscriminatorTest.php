<?php

namespace Nmn\MultiUserBundle\Tests\Unit\Manager;

use Nmn\MultiUserBundle\Tests\Unit\TestCase;
use Nmn\MultiUserBundle\Manager\UserDiscriminator;
use Nmn\MultiUserBundle\Tests\Unit\Stub\UserRegistrationForm;
use Nmn\MultiUserBundle\Tests\Unit\Stub\AnotherUserProfileForm;
use Nmn\MultiUserBundle\Tests\Unit\Stub\User;

class UserDiscriminatorTest extends TestCase
{
    
    public function setUp()
    {
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\Container', array('get')); 
        
        $userParameters = array(
            'entity' => 'Nmn\MultiUserBundle\Tests\Unit\Stub\User',
            'registration' => 'Nmn\MultiUserBundle\Tests\Unit\Stub\UserRegistrationForm',
            'profile' => 'Nmn\MultiUserBundle\Tests\Unit\Stub\UserProfileForm',
            'factory' => ''
        );

        $anotherUserParameters = array(
            'entity' => 'Nmn\MultiUserBundle\Tests\Unit\Stub\AnotherUser',
            'registration' => 'Nmn\MultiUserBundle\Tests\Unit\Stub\AnotherUserRegistrationForm',
            'profile' => 'Nmn\MultiUserBundle\Tests\Unit\Stub\AnotherUserProfileForm',
            'factory' => 'Nmn\MultiUserBundle\Tests\Unit\Stub\CustomUserFactory'
        );
        
        $this->parameters = array('classes' => array('user' => $userParameters, 'anotherUser' => $anotherUserParameters));
        
        $this->discriminator = new UserDiscriminator($this->container, $this->parameters);
                
        $this->session = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session\Session')->disableOriginalConstructor()->getMock();
        
        $this->event = $this->getMockBuilder('Symfony\Component\Security\Http\Event\InteractiveLoginEvent')->disableOriginalConstructor()->getMock();       
        $this->token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken')->disableOriginalConstructor()->getMock();       
        $this->user = new User();  
        $this->userInvalid = $this->getMockBuilder('InvalidUser')->disableOriginalConstructor()->getMock();  
    }

    /**
     * 
     * @return void
     */
    public function testConstructor()
    {
        
        $reflectionClass = new \ReflectionClass("Nmn\MultiUserBundle\Manager\UserDiscriminator");

        $entities               = $reflectionClass->getProperty('entities');
        $registrationFormTypes  = $reflectionClass->getProperty('registrationFormTypes');
        $profileFormTypes       = $reflectionClass->getProperty('profileFormTypes');
        $userFactories          = $reflectionClass->getProperty('userFactories');
        
        $entities->setAccessible(true);
        $registrationFormTypes->setAccessible(true);
        $profileFormTypes->setAccessible(true);
        $userFactories->setAccessible(true);
        
        $entitiesExpected           = array('Nmn\MultiUserBundle\Tests\Unit\Stub\User', 'Nmn\MultiUserBundle\Tests\Unit\Stub\AnotherUser');
        $registrationFormsExpected  = array('Nmn\MultiUserBundle\Tests\Unit\Stub\User' => 'Nmn\MultiUserBundle\Tests\Unit\Stub\UserRegistrationForm', 'Nmn\MultiUserBundle\Tests\Unit\Stub\AnotherUser' => 'Nmn\MultiUserBundle\Tests\Unit\Stub\AnotherUserRegistrationForm');
        $profileFormsExpected       = array('Nmn\MultiUserBundle\Tests\Unit\Stub\User' => 'Nmn\MultiUserBundle\Tests\Unit\Stub\UserProfileForm', 'Nmn\MultiUserBundle\Tests\Unit\Stub\AnotherUser' => 'Nmn\MultiUserBundle\Tests\Unit\Stub\AnotherUserProfileForm');
        $userFactoriesExpected      = array('Nmn\MultiUserBundle\Tests\Unit\Stub\User' => 'Nmn\MultiUserBundle\Manager\UserFactory', 'Nmn\MultiUserBundle\Tests\Unit\Stub\AnotherUser' => 'Nmn\MultiUserBundle\Tests\Unit\Stub\CustomUserFactory');
        
        $this->assertEquals($entitiesExpected, $entities->getValue($this->discriminator));
        $this->assertEquals($registrationFormsExpected, $registrationFormTypes->getValue($this->discriminator));
        $this->assertEquals($profileFormsExpected, $profileFormTypes->getValue($this->discriminator));
        $this->assertEquals($userFactoriesExpected, $userFactories->getValue($this->discriminator));        
    }
    
    /**
     * @expectedException \LogicException
     */
    public function testBuildException()
    {
        $userParameters = array(
            'entity' => 'FakeUser',
            'registration' => 'UserRegistrationForm',
            'profile' => 'UserProfileForm',
            'factory' => 'UserFactory'
        );
        $parameters     = array('classes' => array('user' => $userParameters));
        $discriminator  = new UserDiscriminator($this->container, $parameters);
    }


    /**
     * 
     */
    public function testGetClasses() 
    {
        $this->assertEquals(array('Nmn\MultiUserBundle\Tests\Unit\Stub\User', 'Nmn\MultiUserBundle\Tests\Unit\Stub\AnotherUser'), $this->discriminator->getClasses());
    }
    
    /**
     * @expectedException \LogicException
     */
    public function testSetClassException() 
    {
        $this->discriminator->setClass('ArbitaryClass');
    }
    
    /**
     * 
     */
    public function testSetClassPersist() 
    {        
        $this->container->expects($this->exactly(1))->method('get')->with('session')->will($this->onConsecutiveCalls($this->session));
        $this->session->expects($this->exactly(1))->method('set')->with(UserDiscriminator::SESSION_NAME, 'Nmn\MultiUserBundle\Tests\Unit\Stub\User');        
        $this->discriminator->setClass('Nmn\MultiUserBundle\Tests\Unit\Stub\User', true);
    }
    
    public function testGetClass() 
    {  
        $this->discriminator->setClass('Nmn\MultiUserBundle\Tests\Unit\Stub\AnotherUser');        
        $this->assertEquals('Nmn\MultiUserBundle\Tests\Unit\Stub\AnotherUser', $this->discriminator->getClass());
    }
    
    public function testGetClassDefault() 
    {
        $this->container->expects($this->exactly(1))->method('get')->with('session')->will($this->onConsecutiveCalls($this->session));
        $this->session->expects($this->exactly(1))->method('get')->with(UserDiscriminator::SESSION_NAME, null)->will($this->onConsecutiveCalls(null));
        
        $this->assertEquals('Nmn\MultiUserBundle\Tests\Unit\Stub\User', $this->discriminator->getClass());
    }
    
    public function testGetClassStored() 
    {
        $this->container->expects($this->exactly(1))->method('get')->with('session')->will($this->onConsecutiveCalls($this->session));
        $this->session->expects($this->exactly(1))->method('get')->with(UserDiscriminator::SESSION_NAME, null)->will($this->onConsecutiveCalls('Nmn\MultiUserBundle\Tests\Unit\Stub\AnotherUser'));
        
        $this->assertEquals('Nmn\MultiUserBundle\Tests\Unit\Stub\AnotherUser', $this->discriminator->getClass());
    }
    
    public function testCreateUser()
    {                
        $this->discriminator->setClass('Nmn\MultiUserBundle\Tests\Unit\Stub\User');
        $this->discriminator->createUser();
    }
    
    public function testGetRegistrationForm()
    {
        $type = new UserRegistrationForm;
        $formFactory    = $this->getMock('FormFactory', array('createNamed'));
        
        $this->session->expects($this->exactly(1))->method('get')->with(UserDiscriminator::SESSION_NAME, null)->will($this->onConsecutiveCalls(null));        
        $this->container->expects($this->exactly(2))->method('get')->with($this->logicalOr(
                'session',
                'form.factory'))
                ->will($this->onConsecutiveCalls($formFactory, $this->session));        
        $formFactory->expects($this->exactly(1))->method('createNamed')->with('form_name', $type)->will($this->onConsecutiveCalls(null));
        
        $this->discriminator->getRegistrationForm();
    }
    
    public function testGetProfileForm()
    {
        $type = new AnotherUserProfileForm;
        $formFactory    = $this->getMock('FormFactory', array('createNamed'));
        
        $this->session->expects($this->exactly(1))->method('get')->with(UserDiscriminator::SESSION_NAME, null)->will($this->onConsecutiveCalls('Nmn\MultiUserBundle\Tests\Unit\Stub\AnotherUser'));
             
        $this->container->expects($this->exactly(2))->method('get')->with($this->logicalOr(
                'session',
                'form.factory'))
                ->will($this->onConsecutiveCalls($formFactory, $this->session));        
        $formFactory->expects($this->exactly(1))->method('createNamed')->with('form_name', $type)->will($this->onConsecutiveCalls(null));
        
        $this->discriminator->getProfileForm();
    }
    
    public function testOnSecurityInteractiveLogin()
    {
        $this->event->expects($this->once())->method('getAuthenticationToken')->will($this->returnValue($this->token));
        $this->token->expects($this->once())->method('getUser')->will($this->returnValue($this->user));
        
        $this->container->expects($this->exactly(1))->method('get')->with('session')->will($this->onConsecutiveCalls($this->session));
        $this->session->expects($this->exactly(1))->method('set')->with(UserDiscriminator::SESSION_NAME, 'Nmn\MultiUserBundle\Tests\Unit\Stub\User');
        
        $this->discriminator->onSecurityInteractiveLogin($this->event);
        $this->assertEquals(get_class($this->user), $this->discriminator->getClass());        
    }
    
}