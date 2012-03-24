<?php

namespace Nmn\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class NmnUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
