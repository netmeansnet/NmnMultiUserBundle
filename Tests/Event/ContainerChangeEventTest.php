<?php

namespace PUGX\MultiUserBundle\Tests\Event;

use PUGX\MultiUserBundle\Event\ContainerChangeEvent;
use PUGX\MultiUserBundle\Tests\Stub\User;

class ContainerChangeEventTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {        
        $this->event = new ContainerChangeEvent('name', 'value');
    }
    
    public function testEvent()
    {
        $this->assertEquals('name', $this->event->getParam());
        $this->assertEquals('value', $this->event->getValue());
    }
}