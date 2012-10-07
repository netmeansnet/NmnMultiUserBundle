<?php

namespace PUGX\MultiUserBundle\Tests\Controller;

use PUGX\MultiUserBundle\Listener\ContainerSetterListener;

class ContainerSetterListenerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
                ->disableOriginalConstructor()->getMock();        
        $this->event = $this->getMockBuilder('PUGX\MultiUserBundle\Event\ContainerChangeEvent')
                ->disableOriginalConstructor()->getMock();
        
        $this->listener = new ContainerSetterListener($this->container);
    }
    
    public function testSet()
    {
        $this->event->expects($this->exactly(1))->method('getParam')
                ->will($this->returnValue('param'));        
        $this->event->expects($this->exactly(1))->method('getValue')
                ->will($this->returnValue('value'));
        $this->container->expects($this->exactly(1))->method('set')
                ->with('param', 'value');        
        $this->event->expects($this->exactly(1))->method('setProcessed');
        
        $this->listener->set($this->event);
    }
}