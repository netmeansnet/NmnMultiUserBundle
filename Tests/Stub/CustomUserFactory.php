<?php

namespace PUGX\MultiUserBundle\Tests\Stub;

use PUGX\MultiUserBundle\Model\UserFactoryInterface;

class CustomUserFactory implements UserFactoryInterface
{    
    public static function build($class) {
        return new AnotherUser;
    }
}