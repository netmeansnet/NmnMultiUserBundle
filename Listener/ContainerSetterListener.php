<?php

namespace PUGX\MultiUserBundle\Listener;

use PUGX\MultiUserBundle\Event\ContainerChangeEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This class is the core of the hacking, because it's not a good practice
 * set a container value in this way; anyway it's the only way I found. 
 */
class ContainerSetterListener
{
    /**
     *
     * @var ContainerInterface 
     */
    protected $container;

    /**
     *
     * @param ContainerInterface $container 
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    /**
     *
     * @param ContainerChangeEvent $event 
     */
    public function set(ContainerChangeEvent $event)
    {
        $this->container->set($event->getParam(), $event->getValue());
        $event->setProcessed();
    }
}