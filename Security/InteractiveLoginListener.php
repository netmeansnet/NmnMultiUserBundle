<?php

namespace Nmn\MultiUserBundle\Security;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use FOS\UserBundle\Model\UserInterface;
use Nmn\MultiUserBundle\Manager\UserDiscriminator;

class InteractiveLoginListener
{
    protected $userDiscriminator;

    public function __construct(UserDiscriminator $userDiscriminator)
    {
        $this->userDiscriminator = $userDiscriminator;
    }
    
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof UserInterface) {
            $this->userDiscriminator->setClass(get_class($user), true);
        }
    }
}