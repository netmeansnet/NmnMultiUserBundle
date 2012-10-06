<?php

namespace PUGX\MultiUserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class PUGXMultiUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
