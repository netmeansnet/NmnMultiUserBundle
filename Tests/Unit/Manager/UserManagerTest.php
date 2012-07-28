<?php

namespace Nmn\MultiUserBundle\Tests\Unit\Manager;

use Nmn\MultiUserBundle\Tests\Unit\TestCase;
use Nmn\MultiUserBundle\Manager\OrmUserManager as UserManager;

class UserManagerTest extends TestCase
{
    public function setUp()
    {
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\Container', array('get')); 
        $this->repo      = $this->getMock('Repo', array('findOneBy', 'findAll', 'findBy')); 
        
        $userParameters = array(
            'entity' => 'Nmn\MultiUserBundle\Tests\Unit\Stub\User',
            'registration' => 'Nmn\MultiUserBundle\Tests\Unit\Stub\UserRegistrationForm',
            'profile' => 'Nmn\MultiUserBundle\Tests\Unit\Stub\UserProfileForm',
            'factory' => ''
        );        
        $this->parameters = array('classes' => array('user' => $userParameters));
        
        $this->encoderFactory           = $this->getMock('Symfony\Component\Security\Core\Encoder\EncoderFactory', array(), array(array())); 
        $this->usernameCanonicalizer    = $this->getMock('FOS\UserBundle\Util\Canonicalizer', array()); 
        $this->omailCanonicalizer       = $this->getMock('FOS\UserBundle\Util\Canonicalizer', array()); 
        $this->om                       = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')->disableOriginalConstructor()->getMock();
        $this->class                    = 'User'; 
        $this->userDiscriminator        = $this->getMock('Nmn\MultiUserBundle\Manager\UserDiscriminator', array('createUser', 'getClass', 'getClasses', 'setClass'), array($this->container, $this->parameters)); 
        
        $this->metadata = $this->getMock('\Doctrine\ORM\Mapping\ClassMetadata', array('getName'), array(), '', false);
        
        $this->metadata->expects($this->exactly(1))->method('getName')->will($this->onConsecutiveCalls('Nmn\MultiUserBundle\Tests\Unit\Stub\User'));
        $this->om->expects($this->exactly(1))->method('getClassMetadata')->will($this->returnValue($this->metadata));        
        
        $this->userManager = new UserManager($this->encoderFactory, $this->usernameCanonicalizer, $this->omailCanonicalizer, $this->om, $this->class, $this->userDiscriminator);
    }
        
    
    public function testCreateUser()
    {
        $this->userDiscriminator->expects($this->exactly(1))->method('createUser')->will($this->onConsecutiveCalls(null));
        
        $this->userManager->createUser();
    }
        
    public function testFindUserBy()
    {                
        $this->userDiscriminator->expects($this->exactly(1))->method('getClasses')->will($this->onConsecutiveCalls(array('Nmn\MultiUserBundle\Tests\Unit\Stub\User')));
        $this->om->expects($this->exactly(1))->method('getRepository')->will($this->returnValue($this->repo));
        $this->repo->expects($this->exactly(1))->method('findOneBy')->with(array('criteria' => 'dummy'))->will($this->onConsecutiveCalls(true));        
                
        $this->userManager->findUserBy(array('criteria' => 'dummy'));
    }
    
    /**
     * @expectedException \LogicException
     */
    public function testFindUserByRepoNotFound()
    {                
        $om         = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')->disableOriginalConstructor()->getMock();
        $metadata   = $this->getMock('\Doctrine\ORM\Mapping\ClassMetadata', array('getName'), array(), '', false);        
        $metadata->expects($this->exactly(1))->method('getName')->will($this->onConsecutiveCalls('Nmn\MultiUserBundle\Tests\Unit\Stub\User'));
        $om->expects($this->exactly(1))->method('getClassMetadata')->will($this->returnValue($metadata));               
        $userManager = new UserManager($this->encoderFactory, $this->usernameCanonicalizer, $this->omailCanonicalizer, $om, $this->class, $this->userDiscriminator);
        
        $this->userDiscriminator->expects($this->exactly(1))->method('getClasses')->will($this->onConsecutiveCalls(array('Nmn\MultiUserBundle\Tests\Unit\Stub\User')));
        $om->expects($this->exactly(1))->method('getRepository')->will($this->returnValue(null));   
        $this->repo->expects($this->exactly(0))->method('findOneBy');        
        $this->userDiscriminator->expects($this->exactly(0))->method('setClass');
                
        $userManager->findUserBy(array('criteria' => 'dummy'));
    }
    
    public function testFindUserByUserNotFound()
    {                
        $this->userDiscriminator->expects($this->exactly(1))->method('getClasses')->will($this->onConsecutiveCalls(array('Nmn\MultiUserBundle\Tests\Unit\Stub\User')));
        $this->om->expects($this->exactly(1))->method('getRepository')->will($this->returnValue($this->repo));
        $this->repo->expects($this->exactly(1))->method('findOneBy')->with(array('criteria' => 'dummy'))->will($this->onConsecutiveCalls(null));        
        $this->userDiscriminator->expects($this->exactly(0))->method('setClass');
                
        $user = $this->userManager->findUserBy(array('criteria' => 'dummy'));
        
        $this->assertEquals(null, $user);
    }
    
    public function testFindUsers()
    {                
        $this->userDiscriminator->expects($this->exactly(1))->method('getClasses')->will($this->onConsecutiveCalls(array('Nmn\MultiUserBundle\Tests\Unit\Stub\User')));
        $this->om->expects($this->exactly(1))->method('getRepository')->with('Nmn\MultiUserBundle\Tests\Unit\Stub\User')->will($this->returnValue($this->repo));
        $this->repo->expects($this->exactly(1))->method('findAll')->will($this->onConsecutiveCalls(array()));        
                
        $this->userManager->findUsers();
    }
    
}