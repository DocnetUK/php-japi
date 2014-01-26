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
 * Trivial JAPI bootstrap
 *
 * @author Tom Walder <tom@docnet.nu>
 */

// Includes or Auto-loader
define('BASE_PATH', dirname(dirname(__FILE__)));
require_once(BASE_PATH . '/src/Docnet/JAPI.php');
require_once(BASE_PATH . '/src/Docnet/JAPI/Config.php');
require_once(BASE_PATH . '/src/Docnet/JAPI/Interfaces/Router.php');
require_once(BASE_PATH . '/src/Docnet/JAPI/Router.php');
require_once(BASE_PATH . '/src/Docnet/JAPI/Controller.php');
require_once(BASE_PATH . '/src/Docnet/JAPI/Exceptions/Routing.php');
require_once(BASE_PATH . '/src/Docnet/JAPI/Exceptions/Auth.php');

// Example controller and fake request for this example
require_once('Hello.php');
if(!isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = '/hello/world';
    // $_SERVER['REQUEST_URI'] = '/goodbye';
}

// Run
$api = new \Docnet\JAPI();
$api->getRouter()->addRoute('/goodbye', 'Hello', 'worldAction');
$api->run();
