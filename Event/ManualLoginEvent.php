<?php

namespace PUGX\MultiUserBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class ManualLoginEvent extends Event
{
    private $user;
        
    public function __construct($user)
    {
        $this->user = $user;
    }
    
    public function getUser()
    {
        return $this->user;
    }
}