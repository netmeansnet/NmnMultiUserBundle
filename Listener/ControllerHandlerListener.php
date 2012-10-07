<?php

namespace PUGX\MultiUserBundle\Listener;

use PUGX\MultiUserBundle\Controller\ControllerHandler;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

use FOS\UserBundle\Controller\RegistrationController;
use FOS\UserBundle\Controller\ProfileController;
use FOS\UserBundle\Controller\ResettingController;

class ControllerHandlerListener
{
    /**
     *
     * @var ControllerHandler 
     */
    protected $controllerHandler;
    
    /**
     *
     * @param ControllerHandler $controllerHandler 
     */
    public function __construct(ControllerHandler $controllerHandler)
    {
        $this->controllerHandler = $controllerHandler;
    }
    
    /**
     *
     * @param FilterControllerEvent $event 
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controllers = $event->getController();
        
        if (!is_array($controllers)) {
            return;
        }
        
        $controller = $controllers[0];
        $method     = $controllers[1];
        
        if ($this->isHandledObject($controller)) {
            $this->controllerHandler->handlerPre($controller, $method, $event->getRequest());
        }
    }
    
    /**
     *
     * @param FilterResponseEvent $event 
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $postAction = $event->getRequest()->attributes->get(ControllerHandler::POST_ACTION_NAME);
        if (isset($postAction)) {
            $this->controllerHandler->handlerPost($postAction);
        }
    }
    
    /**
     *
     * @param type $controller 
     */
    protected function isHandledObject($controller)
    {
        $reflectionController = new \ReflectionClass(get_class($controller));
        if ('FOS\UserBundle\Controller' === $reflectionController->getNamespaceName()) {
            return true;
        }
        
        $parentClass = get_parent_class($controller);
        if (false !== $parentClass) {
            $reflectionController = new \ReflectionClass(get_parent_class($controller));
            if ('FOS\UserBundle\Controller' === $reflectionController->getNamespaceName()) {
                return true;
            }
        }
        
        return false;
    }
}