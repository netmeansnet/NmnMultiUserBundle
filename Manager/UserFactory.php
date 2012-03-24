<?php

namespace Nmn\UserBundle\Manager;

/**
 * Description of UserFactory
 * 
 * @author leonardo proietti (leonardo@netmeans.net)
 */
class UserFactory
{
    public static function build($class)
    {        
        $user = new $class;
        
        return $user;
    }
}