<?php

namespace PUGX\MultiUserBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\ResettingController as BaseController;
use PUGX\MultiUserBundle\Event\ManualLoginEvent;

class ResettingController extends BaseController
{
    /**
     * Reset user password
     */
    public function resetAction($token)
    {
        $return = parent::resetAction($token);
        
        if ($return instanceof RedirectResponse) {
            $user = $this->container->get('security.context')->getToken()->getUser();
            if (is_object($user) || $user instanceof UserInterface) {
                $dispatcher = $this->container->get('event_dispatcher');
                $event = new ManualLoginEvent($user);
                $dispatcher->dispatch('security.manual_login', $event);
            }
        }
        
        return $return;
    }
}