<?php

namespace PUGX\MultiUserBundle\Form\Handler;

use FOS\UserBundle\Form\Handler\RegistrationFormHandler as BaseRegistrationFormHandler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use PUGX\MultiUserBundle\Model\UserDiscriminator;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\Form\FormInterface;

class RegistrationFormHandler extends BaseRegistrationFormHandler
{    
    protected $userDiscriminator;

    public function __construct(FormInterface $form, Request $request, UserManagerInterface $userManager, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator, UserDiscriminator $userDiscriminator)
    {
        $form = $userDiscriminator->getRegistrationForm();        
        parent::__construct($form, $request, $userManager, $mailer, $tokenGenerator);
    }    
}