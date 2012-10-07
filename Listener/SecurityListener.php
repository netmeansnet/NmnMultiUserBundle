<?php

namespace PUGX\MultiUserBundle\Listener;

use PUGX\MultiUserBundle\Model\UserDiscriminator;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use PUGX\MultiUserBundle\Event\ManualLoginEvent;

class SecurityListener
{
    /**
     *
     * @var UserDiscriminator 
     */
    protected $userDiscriminator;
    
    /**
     *
     * @param UserDiscriminator $controllerHandler 
     */
    public function __construct(UserDiscriminator $userDiscriminator)
    {
        $this->userDiscriminator = $userDiscriminator;
    }
    
    /**
     *
     * @param InteractiveLoginEvent $event 
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        $this->userDiscriminator->setClass(get_class($user), true);
    }
    
    /**
     * @param ManualLoginEvent $event 
     */
    public function onSecurityManualLogin(ManualLoginEvent $event)
    {
        $user = $event->getUser();
        $this->userDiscriminator->setClass(get_class($user), true);
    }
}