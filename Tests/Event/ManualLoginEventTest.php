<?php

namespace Edimotive\StatsBundle\Tests\Event;

use PUGX\MultiUserBundle\Tests\TestCase;
use PUGX\MultiUserBundle\Event\ManualLoginEvent;
use PUGX\MultiUserBundle\Tests\Stub\User;

class ManualLoginEventTest extends TestCase
{
    public $user;
    
    public function setUp()
    {
        $this->user = new User();
        $this->event = new ManualLoginEvent($this->user);
    }
    
    public function testGetUser()
    {
        $result = $this->event->getUser();
        $this->assertEquals($this->user, $result);
    }
}