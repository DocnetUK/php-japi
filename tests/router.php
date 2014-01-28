<?php
/**
 * Copyright 2014 Docnet
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Test the Router class
 *
 * @author Tom Walder <tom@docnet.nu>
 */

date_default_timezone_set("Europe/London");
require('BaseTest.php');
require('../src/Docnet/JAPI/Config.php');
require('../src/Docnet/JAPI.php');
require('../src/Docnet/JAPI/Exceptions/Routing.php');
require('../src/Docnet/JAPI/Interfaces/Router.php');
require('../src/Docnet/JAPI/Router.php');


/**
 * RouterTests
 *
 * @author Tom Walder <tom@docnet.nu>
 */
class RouterTests extends BaseTest
{

    public function testHelloWorld()
    {
        try {
            $obj_router = new \Docnet\JAPI\Router();
            $obj_router->route('/hello/world');
        } catch (\Exception $obj_ex) {}
        $this->assertIsArray($obj_router->getRouting(), array('\Hello', 'worldAction'));
    }

    public function testMultiPart()
    {
        try {
            $obj_router = new \Docnet\JAPI\Router();
            $obj_router->route('/hello-world/long-name');
        } catch (\Exception $obj_ex) {}
        $this->assertIsArray($obj_router->getRouting(), array('\HelloWorld', 'longNameAction'));
    }

    public function testCasing()
    {
        try {
            $obj_router = new \Docnet\JAPI\Router();
            $obj_router->route('/heLLo-wORld/long-NaMe');
        } catch (\Exception $obj_ex) {}
        $this->assertIsArray($obj_router->getRouting(), array('\HelloWorld', 'longNameAction'));
    }

    protected function testStaticRoute()
    {
        try {
            $obj_router = new \Docnet\JAPI\Router();
            $obj_router->addRoute('/goodbye', 'Testing', 'StaticRoutes');
            $obj_router->route('/goodbye');
        } catch (\Exception $obj_ex) {}
        $this->assertIsArray($obj_router->getRouting(), array('Testing', 'StaticRoutes'));
    }

    protected function testStaticRoutes()
    {
        try {
            $obj_router = new \Docnet\JAPI\Router();
            $obj_router->addRoute('/goodbye', 'Testing', 'StaticRoutes');
            $obj_router->route('/goodbye');
        } catch (\Exception $obj_ex) {}
        $this->assertIsArray($obj_router->getRouting(), array('Testing', 'StaticRoutes'));

        try {
            $obj_router->addRoute('/goodbye/cruel/world', 'TestingAgain', 'MoreStaticRoutes');
            $obj_router->route('/goodbye/cruel/world');
        } catch (\Exception $obj_ex) {}
        $this->assertIsArray($obj_router->getRouting(), array('TestingAgain', 'MoreStaticRoutes'));
    }

}

$obj_tests = new RouterTests();
$obj_tests->run();
