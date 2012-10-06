<?php

namespace PUGX\MultiUserBundle\Form\Handler;

use FOS\UserBundle\Form\Handler\RegistrationFormHandler as BaseRegistrationFormHandler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use PUGX\MultiUserBundle\Manager\UserDiscriminator;

class RegistrationFormHandler extends BaseRegistrationFormHandler
{    
    protected $userDiscriminator;

    public function __construct(Form $form, Request $request, UserManagerInterface $userManager, MailerInterface $mailer, UserDiscriminator $userDiscriminator)
    {
        $this->userDiscriminator = $userDiscriminator;
        $form = $userDiscriminator->getRegistrationForm();
        
        parent::__construct($form, $request, $userManager, $mailer);
    }    
}