<?php

namespace PUGX\MultiUserBundle\Form\Handler;

use FOS\UserBundle\Form\Handler\ProfileFormHandler as BaseProfileFormHandler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use PUGX\MultiUserBundle\Manager\UserDiscriminator;

class ProfileFormHandler extends BaseProfileFormHandler
{    
    protected $userDiscriminator;

    public function __construct(Form $form, Request $request, UserManagerInterface $userManager, UserDiscriminator $userDiscriminator)
    {
        $this->userDiscriminator = $userDiscriminator;
        $form = $userDiscriminator->getProfileForm();
                
        parent::__construct($form, $request, $userManager);
    }
    
}