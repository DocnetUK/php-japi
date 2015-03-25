<?php

require_once('Controllers/Example.php');
require_once('Controllers/World.php');
require_once('Controllers/Three.php');
require_once('Controllers/HeardYoLike.php');

class SolidRouterTest extends PHPUnit_Framework_TestCase
{

    public function testOneWord()
    {
        $obj_router = new \Docnet\JAPI\SolidRouter();
        $obj_router->route('/example');
        $this->assertEquals($obj_router->getController(), '\Example');
    }

    public function testTwoWord()
    {
        $obj_router = new \Docnet\JAPI\SolidRouter();
        $obj_router->route('/hello/world');
        $this->assertEquals($obj_router->getController(), '\Hello\World');
    }

    public function testThreeWord()
    {
        $obj_router = new \Docnet\JAPI\SolidRouter();
        $obj_router->route('/one/two/three');
        $this->assertEquals($obj_router->getController(), '\One\Two\Three');
    }

    public function testCasing()
    {
        $obj_router = new \Docnet\JAPI\SolidRouter();
        $obj_router->route('/hELLo/wORLd');
        $this->assertEquals($obj_router->getController(), '\Hello\World');
    }

    public function testHyphens()
    {
        $obj_router = new \Docnet\JAPI\SolidRouter();
        $obj_router->route('/yo-dawg/heard-yo-like');
        $this->assertEquals($obj_router->getController(), '\YoDawg\HeardYoLike');
    }

    public function testHyphenCasing()
    {
        $obj_router = new \Docnet\JAPI\SolidRouter();
        $obj_router->route('/YO-dAWg/hEArd-yo-liKE');
        $this->assertEquals($obj_router->getController(), '\YoDawg\HeardYoLike');
    }

    public function testOneStatic()
    {
        $obj_router = new \Docnet\JAPI\SolidRouter();
        $obj_router->addRoute('/testing-url', '\\Hello\\World');
        $obj_router->route('/testing-url');
        $this->assertEquals($obj_router->getController(), '\Hello\World');
    }

    public function testSetStatic()
    {
        $obj_router = new \Docnet\JAPI\SolidRouter();
        $obj_router->setRoutes([
            '/testing-url' => '\\Hello\\World',
            '/test' => '\\YoDawg\\HeardYoLike'
        ]);
        $obj_router->route('/testing-url');
        $this->assertEquals($obj_router->getController(), '\Hello\World');
        $obj_router->route('/test');
        $this->assertEquals($obj_router->getController(), '\YoDawg\HeardYoLike');
    }

}
