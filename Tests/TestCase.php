<?php

namespace PUGX\MultiUserBundle\Tests;

/**
 *
 * @author leonardo proietti <leonardo.proietti@gmail.com>
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    public function getEm($repo = null, $connection = null) 
    {
        $em = $this->getMock('\Doctrine\ORM\EntityManager', array('getRepository', 'getClassMetadata', 'persist', 'flush', 'getConnection'), array(), '', false);
        $em->expects($this->any())
                ->method('getRepository')
                ->will($this->returnValue($repo));
        $em->expects($this->any())
                ->method('getClassMetadata')
                ->will($this->returnValue((object) array('name' => 'aClass')));
        $em->expects($this->any())
                ->method('persist')
                ->will($this->returnValue(null));
        $em->expects($this->any())
                ->method('flush')
                ->will($this->returnValue(null));
        $em->expects($this->any())
                ->method('getConnection')
                ->will($this->returnValue($connection));
        
        return $em;
    }
}