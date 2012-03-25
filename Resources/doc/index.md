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
8. Create your controllers

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
    
### 7: Configure parameters for UserDiscriminator
    
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

### 8: Create your controllers

Nmn\UserBundle\Controller\RegistrationController can handle registration flow just for 
the first user passed to discriminator in this case user_one. 
To handle flow of user_two you must configure a route and add a controller in your bundle.

Route configuration

``` yaml
# Acme/UserBundle/Resources/config/routing.yml
user_two_registration:
    pattern:  /register/user-two
    defaults: { _controller: AcmeUserBundle:RegistrationUserTwo:register }
```

Controller 

``` php
<?php

namespace Acme\UserBundle\Controller;

use Nmn\UserBundle\Controller\RegistrationController as BaseController;

class RegistrationUserTwoController extends BaseController
{
    public function registerAction()
    {
        $discriminator = $this->container->get('nmn_user_discriminator');         
        $discriminator->setClass('Acme\UserBundle\Entity\UserTwo'); 
        $form = $discriminator->getRegistrationForm();
        $this->container->set('fos_user.registration.form', $form);
        
        $return = parent::registerAction();
    }
}
```

If you want render your custom view

```php
<?php

namespace Acme\UserBundle\Controller;

use Nmn\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RegistrationUserTwoController extends BaseController
{
    public function registerAction()
    {
        ...
        
        $return = parent::registerAction();
        
        if ($return instanceof RedirectResponse) {
            return $return;
        }
        
        return $this->container->get('templating')->renderResponse('AcmeUserBundle:Registration:user_two.form.html.'.$this->getEngine(), array(
            'form' => $form->createView(),
            'theme' => $this->container->getParameter('fos_user.template.theme'),
        ));   
    }
}
```

You can also define a custom route for UserOne but in this case remember to override the 
RegistrationController in addition to create the route and the controller for UserOne

```php
<?php

namespace Acme\UserBundle\Controller;

use Nmn\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RegistrationController extends BaseController
{
    public function registerAction()
    {
        $url = $this->container->get('router')->generate('home');        
        return new RedirectResponse($url);
    }
}
```


