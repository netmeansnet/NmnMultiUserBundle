<?php

namespace Nmn\MultiUserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class NmnMultiUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
