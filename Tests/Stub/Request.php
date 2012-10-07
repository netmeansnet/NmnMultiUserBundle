<?php

namespace PUGX\MultiUserBundle\Tests\Stub;

use Symfony\Component\HttpFoundation\Request as BaseRequest;

class Request extends BaseRequest
{
    public function __clone()
    {
        
    }
}