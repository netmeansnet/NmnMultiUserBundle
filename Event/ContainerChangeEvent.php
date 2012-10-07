<?php

namespace PUGX\MultiUserBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class ContainerChangeEvent extends Event
{
    private $param;
    private $value;
    public $processed = false;
        
    public function __construct($param, $value)
    {
        $this->param = $param;
        $this->value = $value;
    }
    
    public function getParam()
    {
        return $this->param;
    }    
    
    public function getValue()
    {
        return $this->value;
    }    
    
    public function setProcessed($state = true)
    {
        $this->processed = $state;
    }
    
    public function isProcessed()
    {
        return $this->processed;
    }
}