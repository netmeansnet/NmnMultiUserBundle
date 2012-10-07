<?php

namespace PUGX\MultiUserBundle\Model;

interface UserFactoryInterface
{
    /**
     * @param string $class 
     */
    static function build($class);
}