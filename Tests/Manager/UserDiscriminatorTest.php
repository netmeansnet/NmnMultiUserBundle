<?php

namespace PUGX\MultiUserBundle\Tests\Manager;

use PUGX\MultiUserBundle\Tests\TestCase;
use PUGX\MultiUserBundle\Manager\UserDiscriminator;
use PUGX\MultiUserBundle\Tests\Stub\UserRegistrationForm;
use PUGX\MultiUserBundle\Tests\Stub\AnotherUserProfileForm;
use PUGX\MultiUserBundle\Tests\Stub\User;

class UserDiscriminatorTest extends TestCase
{
    
    public function setUp()
    {
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\Container', array('get')); 
        
        $userParameters = array(
            'entity' => 'PUGX\MultiUserBundle\Tests\Stub\User',
            'registration' => 'PUGX\MultiUserBundle\Tests\Stub\UserRegistrationForm',
            'profile' => 'PUGX\MultiUserBundle\Tests\Stub\UserProfileForm',
            'factory' => ''
        );

        $anotherUserParameters = array(
            'entity' => 'PUGX\MultiUserBundle\Tests\Stub\AnotherUser',
            'registration' => 'PUGX\MultiUserBundle\Tests\Stub\AnotherUserRegistrationForm',
            'profile' => 'PUGX\MultiUserBundle\Tests\Stub\AnotherUserProfileForm',
            'factory' => 'PUGX\MultiUserBundle\Tests\Stub\CustomUserFactory'
        );
        
        $this->parameters = array('classes' => array('user' => $userParameters, 'anotherUser' => $anotherUserParameters));
        
        $this->discriminator = new UserDiscriminator($this->container, $this->parameters);
                
        $this->session = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session')->disableOriginalConstructor()->getMock();
        
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
        
        $reflectionClass = new \ReflectionClass("PUGX\MultiUserBundle\Manager\UserDiscriminator");

        $entities               = $reflectionClass->getProperty('entities');
        $registrationFormTypes  = $reflectionClass->getProperty('registrationFormTypes');
        $profileFormTypes       = $reflectionClass->getProperty('profileFormTypes');
        $userFactories          = $reflectionClass->getProperty('userFactories');
        
        $entities->setAccessible(true);
        $registrationFormTypes->setAccessible(true);
        $profileFormTypes->setAccessible(true);
        $userFactories->setAccessible(true);
        
        $entitiesExpected           = array('PUGX\MultiUserBundle\Tests\Stub\User', 'PUGX\MultiUserBundle\Tests\Stub\AnotherUser');
        $registrationFormsExpected  = array('PUGX\MultiUserBundle\Tests\Stub\User' => 'PUGX\MultiUserBundle\Tests\Stub\UserRegistrationForm', 'PUGX\MultiUserBundle\Tests\Stub\AnotherUser' => 'PUGX\MultiUserBundle\Tests\Stub\AnotherUserRegistrationForm');
        $profileFormsExpected       = array('PUGX\MultiUserBundle\Tests\Stub\User' => 'PUGX\MultiUserBundle\Tests\Stub\UserProfileForm', 'PUGX\MultiUserBundle\Tests\Stub\AnotherUser' => 'PUGX\MultiUserBundle\Tests\Stub\AnotherUserProfileForm');
        $userFactoriesExpected      = array('PUGX\MultiUserBundle\Tests\Stub\User' => 'PUGX\MultiUserBundle\Manager\UserFactory', 'PUGX\MultiUserBundle\Tests\Stub\AnotherUser' => 'PUGX\MultiUserBundle\Tests\Stub\CustomUserFactory');
        
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
        $this->assertEquals(array('PUGX\MultiUserBundle\Tests\Stub\User', 'PUGX\MultiUserBundle\Tests\Stub\AnotherUser'), $this->discriminator->getClasses());
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
        $this->session->expects($this->exactly(1))->method('set')->with(UserDiscriminator::SESSION_NAME, 'PUGX\MultiUserBundle\Tests\Stub\User');        
        $this->discriminator->setClass('PUGX\MultiUserBundle\Tests\Stub\User', true);
    }
    
    public function testGetClass() 
    {  
        $this->discriminator->setClass('PUGX\MultiUserBundle\Tests\Stub\AnotherUser');        
        $this->assertEquals('PUGX\MultiUserBundle\Tests\Stub\AnotherUser', $this->discriminator->getClass());
    }
    
    public function testGetClassDefault() 
    {
        $this->container->expects($this->exactly(1))->method('get')->with('session')->will($this->onConsecutiveCalls($this->session));
        $this->session->expects($this->exactly(1))->method('get')->with(UserDiscriminator::SESSION_NAME, null)->will($this->onConsecutiveCalls(null));
        
        $this->assertEquals('PUGX\MultiUserBundle\Tests\Stub\User', $this->discriminator->getClass());
    }
    
    public function testGetClassStored() 
    {
        $this->container->expects($this->exactly(1))->method('get')->with('session')->will($this->onConsecutiveCalls($this->session));
        $this->session->expects($this->exactly(1))->method('get')->with(UserDiscriminator::SESSION_NAME, null)->will($this->onConsecutiveCalls('PUGX\MultiUserBundle\Tests\Stub\AnotherUser'));
        
        $this->assertEquals('PUGX\MultiUserBundle\Tests\Stub\AnotherUser', $this->discriminator->getClass());
    }
    
    public function testCreateUser()
    {                
        $this->discriminator->setClass('PUGX\MultiUserBundle\Tests\Stub\User');
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
        $formFactory->expects($this->exactly(1))->method('createNamed')->with($type, 'form_name', null, array('validation_groups' => array('Registration', 'Default')))->will($this->onConsecutiveCalls(null));
        
        $this->discriminator->getRegistrationForm();
    }
    
    public function testGetProfileForm()
    {
        $type = new AnotherUserProfileForm;
        $formFactory    = $this->getMock('FormFactory', array('createNamed'));
        
        $this->session->expects($this->exactly(1))->method('get')->with(UserDiscriminator::SESSION_NAME, null)->will($this->onConsecutiveCalls('PUGX\MultiUserBundle\Tests\Stub\AnotherUser'));
             
        $this->container->expects($this->exactly(2))->method('get')->with($this->logicalOr(
                'session',
                'form.factory'))
                ->will($this->onConsecutiveCalls($formFactory, $this->session));        
        $formFactory->expects($this->exactly(1))->method('createNamed')->with($type, 'form_name', null, array('validation_groups' => array('Profile', 'Default')))->will($this->onConsecutiveCalls(null));
        
        $this->discriminator->getProfileForm();
    }
    
    public function testOnSecurityInteractiveLogin()
    {
        $this->event->expects($this->once())->method('getAuthenticationToken')->will($this->returnValue($this->token));
        $this->token->expects($this->once())->method('getUser')->will($this->returnValue($this->user));
        
        $this->container->expects($this->exactly(1))->method('get')->with('session')->will($this->onConsecutiveCalls($this->session));
        $this->session->expects($this->exactly(1))->method('set')->with(UserDiscriminator::SESSION_NAME, 'PUGX\MultiUserBundle\Tests\Stub\User');
        
        $this->discriminator->onSecurityInteractiveLogin($this->event);
        $this->assertEquals(get_class($this->user), $this->discriminator->getClass());        
    }
    
    public function testOnSecurityManualLogin()
    {
        $this->eventManualLogin = $this->getMockBuilder('PUGX\MultiUserBundle\Event\ManualLoginEvent')->disableOriginalConstructor()->getMock();
        $this->eventManualLogin->expects($this->once())->method('getUser')->will($this->returnValue($this->user));
        
        $this->container->expects($this->exactly(1))->method('get')->with('session')->will($this->onConsecutiveCalls($this->session));
        $this->session->expects($this->exactly(1))->method('set')->with(UserDiscriminator::SESSION_NAME, 'PUGX\MultiUserBundle\Tests\Stub\User');
        
        $this->discriminator->onSecurityManualLogin($this->eventManualLogin);
        $this->assertEquals(get_class($this->user), $this->discriminator->getClass());
    }
    
}