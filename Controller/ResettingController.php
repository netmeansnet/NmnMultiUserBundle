<?php

namespace Nmn\MultiUserBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\ResettingController as BaseController;

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
            $discriminator = $this->container->get('nmn_user_discriminator');
            $discriminator->setClass(get_class($user), true);
        }
        
        return $return;
    }
}