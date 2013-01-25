<?php

namespace PUGX\MultiUserBundle\Controller;

use PUGX\MultiUserBundle\Model\UserDiscriminator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use FOS\UserBundle\Controller\RegistrationController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RegistrationManager
{
    /**
     *
     * @var \PUGX\MultiUserBundle\Model\UserDiscriminator 
     */
    protected $userDiscriminator;
    
    /**
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface 
     */
    protected $container;
    
    /**
     *
     * @var \FOS\UserBundle\Controller\RegistrationController 
     */
    protected $controller;
    
    /**
     * 
     * @param \PUGX\MultiUserBundle\Model\UserDiscriminator $userDiscriminator
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(UserDiscriminator $userDiscriminator, ContainerInterface $container, RegistrationController $controller)
    {
        $this->userDiscriminator = $userDiscriminator;
        $this->container = $container;
        $this->controller = $controller;
    }
    
    /**
     * 
     * @param string $class
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function register($class)
    {
        $this->userDiscriminator->setClass($class);
        
        $this->controller->setContainer($this->container);
        $result = $this->controller->registerAction($this->container->get('request'));
        
        if ($result instanceof RedirectResponse) {
            return $result;
        }
        
        $template = $this->userDiscriminator->getRegistrationTemplate();
        if (is_null($template)) {
            $engine = $this->container->getParameter('fos_user.template.engine');
            $template = 'FOSUserBundle:Registration:register.html.'.$engine;
        }
        
        $form = $this->userDiscriminator->getRegistrationForm();
        
        return $this->container->get('templating')->renderResponse($template, array(
            'form' => $form->createView(),
        ));
    }
}