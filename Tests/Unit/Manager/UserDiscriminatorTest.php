<?php

namespace Nmn\UserBundle\Tests\Unit\Manager;

use Nmn\UserBundle\Tests\Unit\TestCase;
use Nmn\UserBundle\Manager\UserDiscriminator;
use Nmn\UserBundle\Tests\Unit\Stub\UserRegistrationForm;
use Nmn\UserBundle\Tests\Unit\Stub\AnotherUserProfileForm;

class UserDiscriminatorTest extends TestCase
{
    
    public function setUp()
    {
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\Container', array('get')); 
        
        $userParameters = array(
            'entity' => 'Nmn\UserBundle\Tests\Unit\Stub\User',
            'registration' => 'Nmn\UserBundle\Tests\Unit\Stub\UserRegistrationForm',
            'profile' => 'Nmn\UserBundle\Tests\Unit\Stub\UserProfileForm',
            'factory' => ''
        );

        $anotherUserParameters = array(
            'entity' => 'Nmn\UserBundle\Tests\Unit\Stub\AnotherUser',
            'registration' => 'Nmn\UserBundle\Tests\Unit\Stub\AnotherUserRegistrationForm',
            'profile' => 'Nmn\UserBundle\Tests\Unit\Stub\AnotherUserProfileForm',
            'factory' => 'Nmn\UserBundle\Tests\Unit\Stub\CustomUserFactory'
        );
        
        $this->parameters = array('classes' => array('user' => $userParameters, 'anotherUser' => $anotherUserParameters));
        
        $this->discriminator = new UserDiscriminator($this->container, $this->parameters);
                
        $this->session = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session')->disableOriginalConstructor()->getMock();
    }

    /**
     * 
     * @return void
     */
    public function testConstructor()
    {
        
        $reflectionClass = new \ReflectionClass("Nmn\UserBundle\Manager\UserDiscriminator");

        $entities               = $reflectionClass->getProperty('entities');
        $registrationFormTypes  = $reflectionClass->getProperty('registrationFormTypes');
        $profileFormTypes       = $reflectionClass->getProperty('profileFormTypes');
        $userFactories          = $reflectionClass->getProperty('userFactories');
        
        $entities->setAccessible(true);
        $registrationFormTypes->setAccessible(true);
        $profileFormTypes->setAccessible(true);
        $userFactories->setAccessible(true);
        
        $entitiesExpected           = array('Nmn\UserBundle\Tests\Unit\Stub\User', 'Nmn\UserBundle\Tests\Unit\Stub\AnotherUser');
        $registrationFormsExpected  = array('Nmn\UserBundle\Tests\Unit\Stub\User' => 'Nmn\UserBundle\Tests\Unit\Stub\UserRegistrationForm', 'Nmn\UserBundle\Tests\Unit\Stub\AnotherUser' => 'Nmn\UserBundle\Tests\Unit\Stub\AnotherUserRegistrationForm');
        $profileFormsExpected       = array('Nmn\UserBundle\Tests\Unit\Stub\User' => 'Nmn\UserBundle\Tests\Unit\Stub\UserProfileForm', 'Nmn\UserBundle\Tests\Unit\Stub\AnotherUser' => 'Nmn\UserBundle\Tests\Unit\Stub\AnotherUserProfileForm');
        $userFactoriesExpected      = array('Nmn\UserBundle\Tests\Unit\Stub\User' => 'Nmn\UserBundle\Manager\UserFactory', 'Nmn\UserBundle\Tests\Unit\Stub\AnotherUser' => 'Nmn\UserBundle\Tests\Unit\Stub\CustomUserFactory');
        
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
        $this->assertEquals(array('Nmn\UserBundle\Tests\Unit\Stub\User', 'Nmn\UserBundle\Tests\Unit\Stub\AnotherUser'), $this->discriminator->getClasses());
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
        $this->session->expects($this->exactly(1))->method('set')->with(UserDiscriminator::SESSION_NAME, 'Nmn\UserBundle\Tests\Unit\Stub\User');        
        $this->discriminator->setClass('Nmn\UserBundle\Tests\Unit\Stub\User', true);
    }
    
    public function testGetClass() 
    {
        $this->container->expects($this->exactly(1))->method('get')->with('session')->will($this->onConsecutiveCalls($this->session));
        $this->session->expects($this->exactly(1))->method('get')->with(UserDiscriminator::SESSION_NAME, null)->will($this->onConsecutiveCalls(null));        
        $this->discriminator->setClass('Nmn\UserBundle\Tests\Unit\Stub\AnotherUser');
        
        $this->assertEquals('Nmn\UserBundle\Tests\Unit\Stub\AnotherUser', $this->discriminator->getClass());
    }
    
    public function testGetClassDefault() 
    {
        $this->container->expects($this->exactly(1))->method('get')->with('session')->will($this->onConsecutiveCalls($this->session));
        $this->session->expects($this->exactly(1))->method('get')->with(UserDiscriminator::SESSION_NAME, null)->will($this->onConsecutiveCalls(null));
        
        $this->assertEquals('Nmn\UserBundle\Tests\Unit\Stub\User', $this->discriminator->getClass());
    }
    
    public function testGetClassStored() 
    {
        $this->container->expects($this->exactly(1))->method('get')->with('session')->will($this->onConsecutiveCalls($this->session));
        $this->session->expects($this->exactly(1))->method('get')->with(UserDiscriminator::SESSION_NAME, null)->will($this->onConsecutiveCalls('Nmn\UserBundle\Tests\Unit\Stub\AnotherUser'));
        
        $this->assertEquals('Nmn\UserBundle\Tests\Unit\Stub\AnotherUser', $this->discriminator->getClass());
    }
    
    public function testCreateUser()
    {
        $this->container->expects($this->exactly(1))->method('get')->with('session')->will($this->onConsecutiveCalls($this->session));
        $this->session->expects($this->exactly(1))->method('get')->with(UserDiscriminator::SESSION_NAME, null)->will($this->onConsecutiveCalls(null));
                
        $this->discriminator->setClass('Nmn\UserBundle\Tests\Unit\Stub\User');
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
        $formFactory->expects($this->exactly(1))->method('createNamed')->with($type, 'form_name', null, array('validation_groups' => array(0 => 'Registration', 1 => 'Default')))->will($this->onConsecutiveCalls(null));
        
        $this->discriminator->getRegistrationForm();
    }
    
    public function testGetProfileForm()
    {
        $type = new AnotherUserProfileForm;
        $formFactory    = $this->getMock('FormFactory', array('createNamed'));
        
        $this->session->expects($this->exactly(1))->method('get')->with(UserDiscriminator::SESSION_NAME, null)->will($this->onConsecutiveCalls('Nmn\UserBundle\Tests\Unit\Stub\AnotherUser'));
             
        $this->container->expects($this->exactly(2))->method('get')->with($this->logicalOr(
                'session',
                'form.factory'))
                ->will($this->onConsecutiveCalls($formFactory, $this->session));        
        $formFactory->expects($this->exactly(1))->method('createNamed')->with($type, 'form_name', null, array('validation_groups' => array(0 => 'Profile', 1 => 'Default')))->will($this->onConsecutiveCalls(null));
        
        $this->discriminator->getProfileForm();
    }
}