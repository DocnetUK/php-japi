<?php
/**
 * Copyright 2015 Docnet
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

use Docnet\JAPI\Controller;
use Docnet\JAPI\Exceptions\Routing as RoutingException;
use Docnet\JAPI\Exceptions\Auth as AuthException;
use Guzzle\Common\Exception\RuntimeException;
use Psr\Log\LoggerAwareInterface;

/**
 * Front controller for our JSON APIs
 *
 * I'm conflicted about whether or not this class adheres to PSR-1 "symbols or
 * side-effects" rule, as one or more of the methods generated output or have
 * side effects (like register_shutdown_function()).
 *
 * @author Tom Walder <tom@docnet.nu>
 */
class JAPI implements LoggerAwareInterface
{

    use HasLogger;

    /**
     * Hook up the shutdown function so we always send nice JSON error responses
     */
    public function __construct()
    {
        register_shutdown_function([$this, 'timeToDie']);
        set_error_handler([$this, 'errorHandler'], E_ALL);
    }

    /**
     * Optionally, encapsulate the bootstrap in a try/catch
     *
     * @param $controller_source
     */
    public function bootstrap($controller_source)
    {
        try {
            $obj_controller = is_callable($controller_source) ? $controller_source() : $controller_source;
            if($obj_controller instanceof Controller) {
                $this->dispatch($obj_controller);
            } else {
                throw new \Exception('Unable to bootstrap', 500);
            }
        } catch (RoutingException $obj_ex) {
            $this->jsonError($obj_ex, 404);
        } catch (AuthException $obj_ex) {
            $this->jsonError($obj_ex, 401);
        } catch (\Exception $obj_ex) {
            $this->jsonError($obj_ex, $obj_ex->getCode());
        }
    }

    /**
     * Go, Johnny, Go!
     *
     * @param Controller $obj_controller
     */
    public function dispatch(Controller $obj_controller)
    {
        $obj_controller->preDispatch();
        $obj_controller->dispatch();
        $obj_controller->postDispatch();
        $this->sendResponse($obj_controller->getResponse());
    }

    /**
     * Custom shutdown function
     *
     * From PHP manual:
     * The following error types cannot be handled with a user defined function:
     * E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR
     * E_CORE_WARNING, E_COMPILE_WARNING
     */
    public function timeToDie()
    {
        $arr_error = error_get_last();
        if ($arr_error && in_array($arr_error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $this->jsonError(new \ErrorException($arr_error['message'], 500, 0, $arr_error['file'], $arr_error['line']), 500);
        }
    }

    /**
     * This function is to complete the error-handling picture by dealing with error types that can be dealt with by
     * user functions. It elevates them to Exceptions.
     *
     * Return true to stop other error handlers executing.
     *
     * @param $int_err
     * @param $str_err
     * @param $str_file
     * @param $int_line
     * @return bool
     * @throws \ErrorException
     */
    public function errorHandler($int_err, $str_err, $str_file, $int_line)
    {
        if(in_array($int_err, [E_RECOVERABLE_ERROR, E_USER_ERROR])) {
            throw new \ErrorException($str_err, $int_err, 0, $str_file, $int_line);
        }
        return true;
    }

    /**
     * Whatever went wrong, let 'em have it in JSON over HTTP
     *
     * @param \Exception $obj_error
     * @param int $int_code
     */
    protected function jsonError(\Exception $obj_error, $int_code)
    {
        $bol_internal = ($obj_error instanceof \ErrorException);
        $str_log_detail = $obj_error->getMessage();
        $str_exception = get_class($obj_error);
        $arr_response = [
            'code' => $int_code,
            'msg' => ($bol_internal ? 'Internal Error' : $str_log_detail)
        ];
        // Adjust the code for HTTP response
        if($int_code < 400 || $int_code > 505) {
            $int_code = 500;
        }
        $this->sendResponse($arr_response, $int_code);
        $this->getLogger()->error("[JAPI] [{$int_code}] [{$str_exception}] {$str_log_detail}");
    }

    /**
     * Output the response as JSON with HTTP headers
     *
     * @param array|object $response
     * @param int $http_code
     */
    protected function sendResponse($response, $http_code = 200)
    {
        $http_code = min(max($http_code, 100), 505);
        http_response_code($http_code);
        header('Content-type: application/json');
        echo json_encode($response);
    }

}