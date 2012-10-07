<?php

namespace PUGX\MultiUserBundle\Tests\Controller;

use PUGX\MultiUserBundle\Listener\ControllerHandlerListener;
use PUGX\MultiUserBundle\Tests\Stub\RegistrationController;
use Symfony\Component\HttpFoundation\ParameterBag;

class ControllerHandlerListenerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->handler = $this->getMockBuilder('PUGX\MultiUserBundle\Controller\ControllerHandler')
                ->disableOriginalConstructor()->getMock();    
        $this->controllerEvent = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\FilterControllerEvent')
                ->disableOriginalConstructor()->getMock();  
        $this->responseEvent = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\FilterResponseEvent')
                ->disableOriginalConstructor()->getMock();  
        
        //mocking a stub that extends Symfony\Component\HttpFoundation\Request solve this error:
        //PHP Fatal error:  __clone method called on non-object in Symfony/Component/HttpFoundation/Request.php on line 382
        //that happen if the request are passed as parameter
        $this->request = $this->getMockBuilder('PUGX\MultiUserBundle\Tests\Stub\Request')
                ->disableOriginalConstructor()->getMock();  
        
        $this->listener = new ControllerHandlerListener($this->handler);
    }
    
    public function testOnKernelController()
    {
        $controller = new RegistrationController();
        $method     = 'confirmAction';
        
        $this->controllerEvent->expects($this->exactly(1))->method('getRequest')
                ->will($this->returnValue($this->request));        
        $this->controllerEvent->expects($this->exactly(1))->method('getController')
                ->will($this->returnValue(array($controller, $method)));        
        $this->handler->expects($this->exactly(1))->method('handlerPre')
                ->with($controller, $method, $this->request);
        
        $this->listener->onKernelController($this->controllerEvent);
    }
    
    public function testOnKernelResponse()
    {
        $this->request->attributes = new ParameterBag(array('pugx_multi_user_post_action' => 'registrationConfirm'));
        
        $this->responseEvent->expects($this->exactly(1))->method('getRequest')
                ->will($this->returnValue($this->request));
        
        $this->handler->expects($this->exactly(1))->method('handlerPost')
                ->with('registrationConfirm');
                
        $this->listener->onKernelResponse($this->responseEvent);
    }
}