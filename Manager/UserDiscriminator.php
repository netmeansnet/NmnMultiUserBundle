<?php

namespace Nmn\MultiUserBundle\Manager;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use FOS\UserBundle\Model\UserInterface;

/**
 * Description of UserDiscriminator
 * 
 * @author leonardo proietti (leonardo@netmeans.net)
 * @author eux (eugenio@netmeans.net)
 */
class UserDiscriminator
{
    const SESSION_NAME = 'nmn_user.user_discriminator.class'; 
    
    protected $serviceContainer;
    
    protected $entities;
    
    protected $registrationFormTypes;
    
    protected $profileFormTypes;
    
    protected $userFactories;
    
    protected $registrationForm = null;
    
    protected $profileForm = null;
    
    protected $class = null;
    
    /**
     *
     * @param ContainerInterface $serviceContainer 
     */
    public function __construct(ContainerInterface $serviceContainer, array $parameters)
    {
        $this->serviceContainer = $serviceContainer;
        
        $config = $this->buildConfig($parameters);
    }
    
    
    /**
     *
     * @param InteractiveLoginEvent $event 
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        $this->setClass(get_class($user), true);
    }
    
    /**
     *
     * @return array 
     */
    public function getClasses()
    {        
        return $this->entities;
    }
    
    
    /**
     *
     * @param string $class 
     */
    public function setClass($class, $persist = false)
    {
        if (!in_array($class, $this->entities)) {
            throw new \LogicException(sprintf('Impossible to set the class discriminator, because the class "%s" is not present in the entities list', $class));
        }
        
        if ($persist) {
            $session = $this->serviceContainer->get('session');
            $session->set(static::SESSION_NAME, $class);
        }
        
        $this->class = $class;
    }
    
    /**
     *
     * @return string 
     */
    public function getClass()
    {       
        if (!is_null($this->class)) {
            return $this->class;
        }
        
        $session     = $this->serviceContainer->get('session');
        $storedClass = $session->get(static::SESSION_NAME, null);

        if ($storedClass) {
            $this->class = $storedClass;
        }
        
        if (is_null($this->class)) {
            $this->class = $this->entities[0];
        }
        
        return $this->class;
    }
    
    /**
     *
     * @return type 
     */
    public function createUser()
    {        
        $class   = $this->getClass();
        $factory = $this->userFactories[$class];
        $user    = $factory::build($class);
                         
        return $user;
    }


    /**
     *
     * @return \Symfony\Component\Form\Form 
     */
    public function getRegistrationForm()
    {           
        if (is_null($this->registrationForm)) {
            $formFactory            = $this->serviceContainer->get('form.factory');
            $type                   = $this->getRegistrationFormType($this->getClass());
            $this->registrationForm = $formFactory->createNamed($type, $type->getName(), null, array('validation_groups' => array(0 => 'Registration', 1 => 'Default')));
        }

        return $this->registrationForm;
    }
    
    /**
     *
     * @return \Symfony\Component\Form\Form 
     */
    public function getProfileForm()
    {                       
        if (is_null($this->profileForm)) {
            $formFactory        = $this->serviceContainer->get('form.factory');
            $type               = $this->getProfileFormType($this->getClass());
            $this->profileForm  = $formFactory->createNamed($type, $type->getName(), null, array('validation_groups' => array(0 => 'Profile', 1 => 'Default')));
        }
                
        return $this->profileForm;
    }
    
    /**
     *
     * @return \Symfony\Component\Form\FormTypeInterface
     * @throws \LogicException 
     */
    protected function getRegistrationFormType($class)
    {
        $className = $this->registrationFormTypes[$class];   
        $type      = new $className($class);
                        
        return $type;
    }

    /**
     *
     * @return \Symfony\Component\Form\FormTypeInterface
     * @throws \LogicException 
     */
    protected function getProfileFormType($class)
    {        
        $className = $this->profileFormTypes[$class];        
        $type      = new $className($class);
                        
        return $type;
    }
        
    /**
     *
     * @param array $entities
     * @param array $registrationForms
     * @param array $profileForms 
     */
    protected function buildConfig(array $parameters)
    {
        $entities               = array();
        $registrationFormTypes  = array();
        $profileFormTypes       = array();
        $userFactoriesTypes     = array();
        
        foreach ($parameters['classes'] as $parameter) {
            
            array_walk($parameter, function($val, $key) use(&$parameter){
                
                if ($key == 'factory' && empty($val)) {
                        $parameter[$key] = 'Nmn\MultiUserBundle\Manager\UserFactory';
                }
                    
                if (!empty($val)) {
                    if (!class_exists($val)) {
                        throw new \LogicException(sprintf('Impossible build discriminator configuration: "%s" not found', $val));
                    }
                }
            });
                        
            $entities[]                                  = $parameter['entity'];
            $registrationFormTypes[$parameter['entity']] = $parameter['registration'];
            $profileFormTypes[$parameter['entity']]      = $parameter['profile'];
            $userFactoriesTypes[$parameter['entity']]    = $parameter['factory'];
        }
        
        $this->entities              = $entities;
        $this->registrationFormTypes = $registrationFormTypes;
        $this->profileFormTypes      = $profileFormTypes;
        $this->userFactories         = $userFactoriesTypes;        
    }
}

?>
