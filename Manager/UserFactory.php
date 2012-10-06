<?php

namespace PUGX\MultiUserBundle\Manager;

/**
 * Description of UserFactory
 * 
 * @author leonardo proietti (leonardo.proietti@gmail.com)
 */
class UserFactory
{
    public static function build($class)
    {        
        $user = new $class;
        
        return $user;
    }
}