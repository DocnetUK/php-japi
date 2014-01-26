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
namespace Docnet;

use \Docnet\JAPI\Exceptions\Routing as RoutingException;
use \Docnet\JAPI\Exceptions\Auth as AuthException;

/**
 * Front controller for our JSON APIs
 *
 * I'm conflicted about whether or not this class adheres to PSR-1 "symbols or
 * side-effects" rule, as one or more of the methods generated output or have
 * side effects (like register_shutdown_function()).
 *
 * @author Tom Walder <tom@docnet.nu>
 */
class JAPI
{

    /**
     * @var JAPI\Config
     */
    private static $obj_config = NULL;

    /**
     * @var null
     */
    private static $flt_startup = NULL;

    /**
     * @var \Docnet\JAPI\Router
     */
    private $obj_router = NULL;

    /**
     * When creating a new JAPI, hook up the shutdown function and set Config
     *
     * @param null|JAPI\Config $obj_config
     */
    public function __construct($obj_config = NULL)
    {
        register_shutdown_function(array($this, 'timeToDie'));
        self::$obj_config = (NULL === $obj_config ? new JAPI\Config() : $obj_config);
        self::$flt_startup = (isset($_SERVER['REQUEST_TIME_FLOAT']) ? $_SERVER['REQUEST_TIME_FLOAT'] : microtime(TRUE));
    }

    /**
     * Go, Johnny, Go!
     */
    public function run()
    {
        try {
            $obj_router = $this->getRouter();
            $obj_router->route();
            $obj_router->dispatch();
        } catch (RoutingException $obj_ex) {
            $this->jsonError($obj_ex, 404);
        } catch (AuthException $obj_ex) {
            $this->jsonError($obj_ex, 401);
        } catch (\Exception $obj_ex) {
            $this->jsonError($obj_ex);
        }
    }

    /**
     * Custom shutdown function
     *
     * @todo Consider checking if headers have already been sent
     * @todo Consider checking isLive before outputting message
     */
    public function timeToDie()
    {
        $arr_error = error_get_last();
        if ($arr_error && in_array($arr_error['type'], array(E_ERROR, E_USER_ERROR, E_COMPILE_ERROR))) {
            $this->jsonError($arr_error['message']);
        }
    }

    /**
     * Whatever went wrong, let 'em have it in JSON
     *
     * One day...
     * @see http://www.php.net/manual/en/function.http-response-code.php
     *
     * @param string|\Exception $mix_message
     * @param int $int_code
     */
    protected function jsonError($mix_message = NULL, $int_code = 500)
    {
        switch ($int_code) {
            case 401:
                header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized", TRUE, 401);
                break;
            case 404:
                header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", TRUE, 404);
                break;
            case 500:
            default:
                header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error", TRUE, 500);
        }
        if ($mix_message instanceof \Exception) {
            $str_message = (self::$obj_config->isLive() ? 'Exception' : get_class($mix_message) . ': ' . $mix_message->getMessage());
        } elseif (is_string($mix_message)) {
            $str_message = $mix_message;
        } else {
            $str_message = 'Unknown error';
        }
        header('Content-type: application/json');
        echo json_encode(array(
            'response' => (int)$int_code,
            'msg' => $str_message
        ));
        exit();
    }

    /**
     * Get the Router
     *
     * @return JAPI\Router
     */
    public function getRouter()
    {
        if (NULL === $this->obj_router) {
            $this->obj_router = new \Docnet\JAPI\Router();
        }
        return $this->obj_router;
    }

    /**
     * Set a custom Router
     *
     * @param JAPI\Interfaces\Router $obj_router
     */
    public function setRouter(\Docnet\JAPI\Interfaces\Router $obj_router)
    {
        $this->obj_router = $obj_router;
    }

    /**
     * Get the running config
     *
     * @return JAPI\Config|null
     */
    public static function getConfig()
    {
        return self::$obj_config;
    }

    /**
     * Get the start time as a microtime float
     *
     * @return float
     */
    public static function get_start_time()
    {
        return self::$flt_startup;
    }

}