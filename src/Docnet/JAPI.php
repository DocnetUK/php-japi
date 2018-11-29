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
use \Docnet\JAPI\Exceptions\Maintenance as MaintenanceException;
use Docnet\JAPI\Exceptions\AccessDenied as AccessDeniedException;

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
    private static $obj_config = null;

    /**
     * @var JAPI\Router
     */
    private static $obj_router = null;

    /**
     * @var JAPI\Logger
     */
    private $obj_logger = null;

    /**
     * @var null|float
     */
    private static $flt_startup = null;

    /**
     * When creating a new JAPI, hook up the shutdown function and set Config
     *
     * @param null|JAPI\Config $obj_config
     */
    public function __construct($obj_config = null)
    {
        register_shutdown_function(array($this, 'timeToDie'));
        if (null !== $obj_config) {
            self::$obj_config = $obj_config;
        }
        self::$flt_startup = (isset($_SERVER['REQUEST_TIME_FLOAT']) ? $_SERVER['REQUEST_TIME_FLOAT'] : microtime(true));
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

        } catch (MaintenanceException $obj_ex) {
            $this->jsonError('Service Temporarily Unavailable', 503);

        } catch (RoutingException $obj_ex) {
            $this->jsonError($obj_ex, 404);

        } catch (AuthException $obj_ex) {
            $this->jsonError($obj_ex, 401);

        } catch (AccessDeniedException $obj_ex) {
            $this->jsonError($obj_ex, 403);

        } catch (\Exception $obj_ex) {
            $this->jsonError($obj_ex);
        }
    }

    /**
     * Custom shutdown function
     *
     * @todo Consider checking if headers have already been sent
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
     *
     * @see http://www.php.net/manual/en/function.http-response-code.php
     *
     * @param string|\Exception $mix_message
     * @param int $int_code
     */
    protected function jsonError($mix_message = null, $int_code = 500)
    {
        switch ($int_code) {
            case 401:
                header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized", true, 401);
                break;
            case 403:
                header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden", true, 401);
                break;
            case 404:
                header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
                break;
            case 503:
                header($_SERVER["SERVER_PROTOCOL"] . " 503 Service Unavailable", true, 503);
                break;
            case 500:
            default:
                header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error", true, 500);
        }
        if ($mix_message instanceof \Exception) {
            $str_log = get_class($mix_message) . ': ' . $mix_message->getMessage();
            $str_message = self::getConfig()->isLive() ? 'Exception' : $str_log;
        } elseif (is_string($mix_message)) {
            $str_log = $str_message = $mix_message;
        } else {
            $str_log = $str_message = 'Unknown error';
        }
        header('Content-type: application/json');
        echo json_encode(
            array(
                'response' => (int)$int_code,
                'msg' => $str_message,
            )
        );
        $this->log(LOG_ERR, "[JAPI exiting with {$int_code}] " . $str_log);
        exit();
    }

    /**
     * Get the Router
     *
     * @return JAPI\Router
     */
    public static function getRouter()
    {
        if (null === self::$obj_router) {
            self::$obj_router = new JAPI\Router();
        }
        return self::$obj_router;
    }

    /**
     * Set a custom Router
     *
     * @param JAPI\Interfaces\Router $obj_router
     */
    public function setRouter(JAPI\Interfaces\Router $obj_router)
    {
        self::$obj_router = $obj_router;
    }

    /**
     * Get the running config
     *
     * @return JAPI\Config|null
     */
    public static function getConfig()
    {
        if (null === self::$obj_config) {
            self::$obj_config = new JAPI\Config();
        }
        return self::$obj_config;
    }

    /**
     * Get the execution time in seconds, rounded
     *
     * @param int $int_dp
     * @return float
     */
    public static function getDuration($int_dp = 4)
    {
        return round(microtime(true) - self::$flt_startup, $int_dp);
    }

    /**
     * Log to the current Logger, create one if needed
     *
     * @param $int_level
     * @param $str_message
     */
    protected function log($int_level, $str_message)
    {
        if (null === $this->obj_logger) {
            $this->obj_logger = new JAPI\Logger();
        }
        $this->obj_logger->log($int_level, $str_message);
    }

    /**
     * Set a custom Logger
     *
     * @param JAPI\Interfaces\Logger $obj_logger
     */
    public function setLogger(JAPI\Interfaces\Logger $obj_logger)
    {
        $this->obj_logger = $obj_logger;
    }

}