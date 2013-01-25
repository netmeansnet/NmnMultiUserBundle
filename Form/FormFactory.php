<?php

namespace PUGX\MultiUserBundle\Form;

use PUGX\MultiUserBundle\Model\UserDiscriminator;
use FOS\UserBundle\Form\Factory\FactoryInterface;

class FormFactory implements FactoryInterface
{
    /**
     *
     * @var \PUGX\MultiUserBundle\Model\UserDiscriminator 
     */
    private $userDiscriminator;
    
    /**
     *
     * @var string 
     */
    private $type;
    
    /**
     * 
     * @param \PUGX\MultiUserBundle\Model\UserDiscriminator $userDiscriminator
     * @param string $type registration|profile
     */
    public function __construct(UserDiscriminator $userDiscriminator, $type) 
    {
        $this->userDiscriminator = $userDiscriminator;
        $this->type = $type;
    }
    
    /**
     * 
     * @return \Symfony\Component\Form\Form 
     */
    public function createForm()
    {
        return $this->userDiscriminator->getForm($this->type);
    }
}