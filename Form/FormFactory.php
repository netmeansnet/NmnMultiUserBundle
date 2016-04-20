<?php

namespace PUGX\MultiUserBundle\Form;

use PUGX\MultiUserBundle\Model\UserDiscriminator;
use Symfony\Component\Form\FormFactoryInterface;
use FOS\UserBundle\Form\Factory\FactoryInterface;

class FormFactory implements FactoryInterface
{
    /** @var \PUGX\MultiUserBundle\Model\UserDiscriminator */
    private $userDiscriminator;
    
    /**  @var FormFactoryInterface */
    private $formFactory;
    
    /** @var string */
    private $type;
    
    /** @var array  */
    private $forms = array();

    /**
     * @param UserDiscriminator    $userDiscriminator
     * @param FormFactoryInterface $formFactory
     * @param string               $type              registration|profile
     */
    public function __construct(UserDiscriminator $userDiscriminator, FormFactoryInterface $formFactory, $type) 
    {
        $this->userDiscriminator = $userDiscriminator;
        $this->formFactory = $formFactory;
        $this->type = $type;
    }
    
    /**
     * @return \Symfony\Component\Form\Form 
     */
    public function createForm()
    {
        $type = $this->userDiscriminator->getFormType($this->type);
        $name = $this->userDiscriminator->getFormName($this->type);
        $validationGroups = $this->userDiscriminator->getFormValidationGroups($this->type);
        
        if (array_key_exists($name, $this->forms)) {
            return $this->forms[$name];
        }

        if (\Symfony\Component\HttpKernel\Kernel::MAJOR_VERSION >= 3) {
            $form = $this->formFactory->createNamed(
                $name,
                get_class($type),
                null,
                array('validation_groups' => $validationGroups)
            );
        } else {
            // Legacy support
            $form = $this->formFactory->createNamed(
                $name,
                $type,
                null,
                array('validation_groups' => $validationGroups)
            );
        }
        
        $this->forms[$name] = $form;
        
        return $form;
    }
}
