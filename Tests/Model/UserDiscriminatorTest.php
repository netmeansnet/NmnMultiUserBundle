<?php

namespace PUGX\MultiUserBundle\Tests\Model;

use PUGX\MultiUserBundle\Model\UserDiscriminator;
use PUGX\MultiUserBundle\Tests\Stub\UserRegistrationForm;
use PUGX\MultiUserBundle\Tests\Stub\UserProfileForm;
use PUGX\MultiUserBundle\Tests\Stub\AnotherUserRegistrationForm;
use PUGX\MultiUserBundle\Tests\Stub\AnotherUserProfileForm;
use PUGX\MultiUserBundle\Tests\Stub\User;
use PUGX\MultiUserBundle\Tests\Stub\AnotherUser;
use Symfony\Component\Form\FormFactoryInterface;

class UserDiscriminatorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->session = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session\Session')->disableOriginalConstructor()->getMock();
        $this->formFactory = $this->getMockBuilder('Symfony\Component\Form\FormFactoryInterface')->disableOriginalConstructor()->getMock();
        $this->event = $this->getMockBuilder('Symfony\Component\Security\Http\Event\InteractiveLoginEvent')->disableOriginalConstructor()->getMock();       
        $this->token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken')->disableOriginalConstructor()->getMock();      
        $this->user = new User();  
        $this->userInvalid = $this->getMockBuilder('InvalidUser')->disableOriginalConstructor()->getMock();  
        $this->userFactory = $this->getMockBuilder('PUGX\MultiUserBundle\Model\UserFactoryInterface')->disableOriginalConstructor()->getMock();
                
        $userParameters = array(
            'entity' => array(
                'class' => 'PUGX\MultiUserBundle\Tests\Stub\User',
                'factory' => 'PUGX\MultiUserBundle\Model\UserFactory'
            ),
            'registration' => array(
                'form' => 'PUGX\MultiUserBundle\Tests\Stub\UserRegistrationForm',
                'options' => array(
                    'validation_groups' => array('Registration', 'Default')
                ),
                'template' => 'AcmeUserBundle:Registration:user_one.form.html.twig'
            ),
            'profile' => array(
                'form' => 'PUGX\MultiUserBundle\Tests\Stub\UserProfileForm',
                'options' => array(
                    'validation_groups' => array('Profile', 'Default')
                )
            )
        );

        $anotherUserParameters = array(
            'entity' => array(
                'class' => 'PUGX\MultiUserBundle\Tests\Stub\AnotherUser',
                'factory' => 'PUGX\MultiUserBundle\Tests\Stub\CustomUserFactory'
            ),
            'registration' => array(
                'form' => 'PUGX\MultiUserBundle\Tests\Stub\AnotherUserRegistrationForm',
                'options' => array(
                    'validation_groups' => array('Registration', 'Default')
                ),
                'template' => 'AcmeUserBundle:Registration:user_two.form.html.twig'
            ),
            'profile' => array(
                'form' => 'PUGX\MultiUserBundle\Tests\Stub\AnotherUserProfileForm',
                'options' => array(
                    'validation_groups' => array('Profile', 'Default')
                )
            )
        );
        
        $this->parameters = array('user_one' => $userParameters, 'user_two' => $anotherUserParameters);
        
        $this->discriminator = new UserDiscriminator($this->session, $this->formFactory, $this->parameters);
    }
    
    public function testInit()
    {        
        $reflectionClass = new \ReflectionClass($this->discriminator);

        $entities               = $reflectionClass->getProperty('entities');
        $registrationFormTypes  = $reflectionClass->getProperty('registrationFormTypes');
        $profileFormTypes       = $reflectionClass->getProperty('profileFormTypes');
        $userFactories          = $reflectionClass->getProperty('userFactories');
        $registrationTemplates  = $reflectionClass->getProperty('registrationTemplates');
        
        $entities->setAccessible(true);
        $registrationFormTypes->setAccessible(true);
        $profileFormTypes->setAccessible(true);
        $userFactories->setAccessible(true);
        $registrationTemplates->setAccessible(true);
        
        $entitiesExpected           = array('PUGX\MultiUserBundle\Tests\Stub\User', 'PUGX\MultiUserBundle\Tests\Stub\AnotherUser');
        
        $registrationFormsExpected  = array('PUGX\MultiUserBundle\Tests\Stub\User' => 'PUGX\MultiUserBundle\Tests\Stub\UserRegistrationForm', 
                                            'PUGX\MultiUserBundle\Tests\Stub\AnotherUser' => 'PUGX\MultiUserBundle\Tests\Stub\AnotherUserRegistrationForm');
        
        $registrationTemplatesExpected  = array('PUGX\MultiUserBundle\Tests\Stub\User' => 'AcmeUserBundle:Registration:user_one.form.html.twig', 
                                            'PUGX\MultiUserBundle\Tests\Stub\AnotherUser' => 'AcmeUserBundle:Registration:user_two.form.html.twig');
        
        $profileFormsExpected       = array('PUGX\MultiUserBundle\Tests\Stub\User' => 'PUGX\MultiUserBundle\Tests\Stub\UserProfileForm', 
                                            'PUGX\MultiUserBundle\Tests\Stub\AnotherUser' => 'PUGX\MultiUserBundle\Tests\Stub\AnotherUserProfileForm');
        
        $userFactoriesExpected      = array('PUGX\MultiUserBundle\Tests\Stub\User' => 'PUGX\MultiUserBundle\Model\UserFactory', 
                                            'PUGX\MultiUserBundle\Tests\Stub\AnotherUser' => 'PUGX\MultiUserBundle\Tests\Stub\CustomUserFactory');
        
        $this->assertEquals($entitiesExpected, $entities->getValue($this->discriminator));
        $this->assertEquals($registrationFormsExpected, $registrationFormTypes->getValue($this->discriminator));
        $this->assertEquals($profileFormsExpected, $profileFormTypes->getValue($this->discriminator));
        $this->assertEquals($userFactoriesExpected, $userFactories->getValue($this->discriminator));
        $this->assertEquals($registrationTemplatesExpected, $registrationTemplates->getValue($this->discriminator));
    }
    
    /**
     * @expectedException \LogicException
     */
    public function testBuildException()
    {        
        $userParameters = array(
            'entity' => array(
                'class' => 'FakeUser',
                'factory' => 'PUGX\MultiUserBundle\Model\UserFactory'
            ),
            'registration' => array(
                'form' => 'PUGX\MultiUserBundle\Tests\Stub\UserRegistrationForm',
                'options' => array(
                    'validation_groups' => array('Registration', 'Default')
                )
            ),
            'profile' => array(
                'form' => 'PUGX\MultiUserBundle\Tests\Stub\UserProfileForm',
                'options' => array(
                    'validation_groups' => array('Profile', 'Default')
                )
            )
        );
        
        $parameters     = array('user' => $userParameters);
        $discriminator  = new UserDiscriminator($this->session, $this->formFactory, $parameters);
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
    
    public function testGetClass() 
    {  
        $this->discriminator->setClass('PUGX\MultiUserBundle\Tests\Stub\AnotherUser');        
        $this->assertEquals('PUGX\MultiUserBundle\Tests\Stub\AnotherUser', $this->discriminator->getClass());
    }
    
    public function testSetClassPersist() 
    {        
        $this->session->expects($this->exactly(1))->method('set')->with(UserDiscriminator::SESSION_NAME, 'PUGX\MultiUserBundle\Tests\Stub\User');        
        $this->discriminator->setClass('PUGX\MultiUserBundle\Tests\Stub\User', true);
    }
    
    public function testGetClassDefault() 
    {
        $this->session->expects($this->exactly(1))->method('get')->with(UserDiscriminator::SESSION_NAME, null)->will($this->onConsecutiveCalls(null));        
        $this->assertEquals('PUGX\MultiUserBundle\Tests\Stub\User', $this->discriminator->getClass());
    }
    
    public function testGetClassStored() 
    {
        $this->session->expects($this->exactly(1))->method('get')->with(UserDiscriminator::SESSION_NAME, null)->will($this->onConsecutiveCalls('PUGX\MultiUserBundle\Tests\Stub\AnotherUser'));
        $this->assertEquals('PUGX\MultiUserBundle\Tests\Stub\AnotherUser', $this->discriminator->getClass());
    }
    
    public function testCreateUser()
    {        
        $expected = new AnotherUser();
        $this->session->expects($this->exactly(0))->method('get');   
        
        $this->discriminator->setClass('PUGX\MultiUserBundle\Tests\Stub\AnotherUser');
        $result = $this->discriminator->createUser();
        $this->assertEquals($expected, $result);
    }
    
    public function testGetDefaultRegistrationForm()
    {
        $type = new UserRegistrationForm;        
        $this->session->expects($this->exactly(1))->method('get')->with(UserDiscriminator::SESSION_NAME, null)->will($this->onConsecutiveCalls(null));         
        $this->formFactory->expects($this->exactly(1))->method('createNamed')
                ->with('form_name', $type, null, array('validation_groups' => array('Registration', 'Default')))
                ->will($this->onConsecutiveCalls(null));
        
        $this->discriminator->getForm('registration');
    }
    
    public function testGetAnotherRegistrationForm()
    {
        $type = new AnotherUserRegistrationForm;
        $this->session->expects($this->exactly(0))->method('get');         
        $this->formFactory->expects($this->exactly(1))->method('createNamed')
                ->with('form_name', $type, null, array('validation_groups' => array('Registration', 'Default')))
                ->will($this->onConsecutiveCalls(null));
        
        $this->discriminator->setClass('PUGX\MultiUserBundle\Tests\Stub\AnotherUser');
        $this->discriminator->getForm('registration');
    }
    
    public function testGetDefaultProfileForm()
    {
        $type = new UserProfileForm;        
        $this->session->expects($this->exactly(1))->method('get')->with(UserDiscriminator::SESSION_NAME, null)->will($this->onConsecutiveCalls(null));         
        $this->formFactory->expects($this->exactly(1))->method('createNamed')
                ->with('form_name', $type, null, array('validation_groups' => array('Profile', 'Default')))
                ->will($this->onConsecutiveCalls(null));
        
        $this->discriminator->getForm('profile');
    }
    
    public function testGetAnotherProfileForm()
    {
        $type = new AnotherUserProfileForm;        
        $this->session->expects($this->exactly(0))->method('get');         
        $this->formFactory->expects($this->exactly(1))->method('createNamed')
                ->with('form_name', $type, null, array('validation_groups' => array('Profile', 'Default')))
                ->will($this->onConsecutiveCalls(null));
        
        $this->discriminator->setClass('PUGX\MultiUserBundle\Tests\Stub\AnotherUser');
        $this->discriminator->getForm('profile');
    }
}
    