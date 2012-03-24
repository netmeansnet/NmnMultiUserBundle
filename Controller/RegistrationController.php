<?php

namespace Nmn\UserBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\RegistrationController as BaseController;

class RegistrationController extends BaseController
{
    public function registerAction()
    {
        $discriminator = $this->container->get('nmn_user_discriminator');
        $form = $discriminator->getRegistrationForm();
        $this->container->set('fos_user.registration.form', $form);
        
        $return = parent::registerAction();
        
        return $return;
    }
}