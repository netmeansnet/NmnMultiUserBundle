<?php

namespace PUGX\MultiUserBundle\Tests\Controller;

use PUGX\MultiUserBundle\Controller\ControllerAwareFactory;
use FOS\UserBundle\Controller\RegistrationController;

class ControllerAwareFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
                ->disableOriginalConstructor()->getMock();
        
        $this->factory = new ControllerAwareFactory($this->container);
    }
    
    public function testBuildRegistration()
    {
        $result = $this->factory->build('Registration');
        $this->assertEquals('FOS\UserBundle\Controller\RegistrationController', get_class($result));
    }
}