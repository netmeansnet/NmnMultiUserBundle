<?php

namespace Nmn\MultiUserBundle\Manager;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;
use FOS\UserBundle\Util\CanonicalizerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Validator\Constraint;
use Nmn\MultiUserBundle\Manager\UserDiscriminator;

/**
 * Custom user manager for FOSUserBundle
 *
 * @author leonardo proietti (leonardo@netmeans.net)
 * @author eux (eugenio@netmeans.net)
 */
class OrmUserManager extends BaseUserManager
{
    protected $em;
    protected $class;    
    protected $userDiscriminator;

    /**
     * Constructor.
     *
     * @param EncoderFactoryInterface $encoderFactory
     * @param CanonicalizerInterface  $usernameCanonicalizer
     * @param CanonicalizerInterface  $emailCanonicalizer
     * @param EntityManager           $em
     * @param string                  $class
     */
    public function __construct(EncoderFactoryInterface $encoderFactory, CanonicalizerInterface $usernameCanonicalizer, CanonicalizerInterface $emailCanonicalizer, EntityManager $em, $class, UserDiscriminator $userDiscriminator)
    {
        $this->em = $em;
        $this->userDiscriminator = $userDiscriminator;
        
        parent::__construct($encoderFactory, $usernameCanonicalizer, $emailCanonicalizer, $em, $class);
    }
    
    public function createUser()
    {
        return $this->userDiscriminator->createUser();
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritDoc}
     */
    public function findUserBy(array $criteria)
    {        
        $classes = $this->userDiscriminator->getClasses();
                
        foreach ($classes as $class) {

            $repo = $this->em->getRepository($class);
            
            if (!$repo) {
                throw new \LogicException(sprintf('Repository "%s" not found', $class));
            }
                        
            $user = $repo->findOneBy($criteria);
            
            if ($user) {                
                return $user;
            }
        }
        
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function findUsers()
    {
        $classes = $this->userDiscriminator->getClasses();
        
        $users = array();
        foreach ($classes as $class) {
            $repo = $this->em->getRepository($class);
                        
            $users = $repo->findAll();
            
            if ($users) {
                $users = array_merge($users, $users);
            }               
        }
        
        return $users;
    }


    /**
     * Gets conflictual users for the given user and constraint.
     *
     * @param UserInterface $value
     * @param array         $fields
     * @return array
     */
    protected function findConflictualUsers($value, array $fields)
    {
        $classes = $this->userDiscriminator->getClasses();
                
        foreach ($classes as $class) {

            $repo = $this->em->getRepository($class);
                        
            $users = $repo->findBy($this->getCriteria($value, $fields));
            
            if (count($users) > 0) {
                
                return $users;
            }
        }

        return array();
    }

}