NmnUserBundle Documentation
==================================

NmnUserBundle came by the need to use different types of users using only one fos_user service. 
In practice it is an hack that force FOSUser bundle through custom UserManager, controllers and forms handlers.
It 's just a lazy way to use free, most of the functionality of FOSUserBundle.
This bundle has been realized as a part of a real application that uses doctrine orm, 
so for now it only supports the ORM db driver.

## Prerequisites

[FOSUserBundle] (https://github.com/FriendsOfSymfony/FOSUserBundle)

## Installation

1. Download NmnUserBundle
2. Configure the Autoloader
3. Enable the Bundle
4. Create your UserBundle
5. Create your Entities
6. Configure the FOSUserBundle (NmnUserBundle params)
7. Configure parameters for UserDiscriminator

### 1: Download NmnUserBundle

**Using the vendors script**

Add the following lines in your `deps` file:

```
[NmnUserBundle]
    git=git://github.com/netmeansnet/NmnUserBundle.git
    target=bundles/Nmn/UserBundle
```

Now, run the vendors script to download the bundle:

``` bash
$ php bin/vendors install
```

### 2: Configure the Autoloader

Add the `Nmn` namespace to your autoloader:

``` php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    'Nmn' => __DIR__.'/../vendor/bundles',
));
```

### 3: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Nmn\UserBundle\NmnUserBundle(),
    );
}
```

### 4: Create your UserBundle

Create a bundle that extends FOSUserBundle

``` php
<?php
namespace Acme\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AcmeUserBundle extends Bundle
{
    public function getParent()
    {
        return 'NmnUserBundle';
    }
}
```

### 5: Create your Entities

Create entities using Doctrine2 inheritance.

Abstract User that directly extends from FOS\UserBundle\Entity\User

``` php
<?php

namespace Acme\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\User as BaseUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"user_one" = "UserOne", "user_two" = "UserTwo"})
 * 
 */
abstract class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
}
```

UserOne

``` php
<?php

namespace Acme\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_one")
 * 
 */
class UserOne extends User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
}
```

UserTwo

``` php
<?php

namespace Acme\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_two")
 * 
 */
class UserTwo extends User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
}
```

You must also create forms for your entities: 
see [Overriding Default FOSUserBundle Forms] (https://github.com/FriendsOfSymfony/FOSUserBundle/blob/master/Resources/doc/overriding_forms.md)

### 6: Configure the FOSUserBundle (NmnUserBundle params)

Keep in mind that NmnUserBundle overwrites user_class via UserDiscriminator
but does it only in controllers and forms handlers; in other case (command, sonata integration, etc)
it is still used the user_class configured in the config.

``` yaml
# Acme/UserBundle/Resources/config/config.yml
fos_user:
    db_driver: orm
    firewall_name: main
    user_class: Acme\UserBundle\Entity\User
    service:
        user_manager: nmn_user_manager
    registration:
        form:
            handler: nmn_user_registration_form_handler
    profile:
        form:
            handler: nmn_user_profile_form_handler
```
    
### 6: Configure parameters for UserDiscriminator
    
``` yaml
# Acme/UserBundle/Resources/config/config.yml

parameters:    
  nmn_user_discriminator_parameters:
    classes:
        user_one:
            entity: Acme\UserBundle\Entity\UserOne
            registration: Acme\UserBundle\Form\Type\RegistrationUserOneFormType
            profile: Acme\UserBundle\Form\Type\ProfileUserOneFormType
            factory: 
        user_two:
            entity: Acme\UserBundle\Entity\UserTwo
            registration: Acme\UserBundle\Form\Type\RegistrationUserTwoFormType
            profile: Acme\UserBundle\Form\Type\ProfileUserTwoFormType
            factory: 
```