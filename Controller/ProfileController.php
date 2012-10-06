<?php

namespace PUGX\MultiUserBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\ProfileController as BaseController;

class ProfileController extends BaseController
{
    public function editAction()
    {                
        $discriminator = $this->container->get('pugx_user_discriminator');
        $form          = $discriminator->getProfileForm();
        $this->container->set('fos_user.profile.form', $form);
        
        $return = parent::editAction();
        
        return $return;
    }
}