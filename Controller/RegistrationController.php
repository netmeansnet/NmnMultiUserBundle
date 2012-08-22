<?php

namespace Nmn\MultiUserBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Nmn\MultiUserBundle\Event\ManualLoginEvent;
use FOS\UserBundle\Model\UserInterface;

class RegistrationController extends BaseController
{
    public function registerAction()
    {
        $discriminator = $this->container->get('nmn_user_discriminator');
        $form = $discriminator->getRegistrationForm();
        $this->container->set('fos_user.registration.form', $form);
        
        $return = parent::registerAction();
        
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
    
    public function confirmAction($token)
    {
        $return = parent::confirmAction($token);
        
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