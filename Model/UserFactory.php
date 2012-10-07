<?php

namespace PUGX\MultiUserBundle\Model;

use PUGX\MultiUserBundle\Model\UserFactoryInterface;

/**
 * @author leonardo proietti (leonardo.proietti@gmail.com)
 */
class UserFactory implements UserFactoryInterface
{
    /**
     *
     * @param type $class
     * @return \PUGX\MultiUserBundle\Model\class 
     */
    public static function build($class)
    {        
        $user = new $class;        
        return $user;
    }
}