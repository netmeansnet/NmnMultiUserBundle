<?php

namespace Edimotive\StatsBundle\Tests\Unit\Event;

use Nmn\MultiUserBundle\Tests\Unit\TestCase;
use Nmn\MultiUserBundle\Event\ManualLoginEvent;
use Nmn\MultiUserBundle\Tests\Unit\Stub\User;

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