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
     * Should we expose detailed error information in responses?
     *
     * @var bool
     */
    private $bol_expose_errors = false;

    /**
     * Hook up the shutdown function so we always send nice JSON error responses
     */
    public function __construct()
    {
        register_shutdown_function([$this, 'timeToDie']);
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
     */
    public function timeToDie()
    {
        $arr_error = error_get_last();
        if ($arr_error && in_array($arr_error['type'], [E_ERROR, E_USER_ERROR, E_COMPILE_ERROR])) {
            $this->jsonError(new \ErrorException($arr_error['message'], 500, 0, $arr_error['file'], $arr_error['line']), 500);
        }
    }

    /**
     * Whatever went wrong, let 'em have it in JSON over HTTP
     *
     * @param \Exception $obj_error
     * @param int $int_code
     */
    protected function jsonError(\Exception $obj_error, $int_code)
    {
        $arr_response = [
            'code' => $int_code,
            'msg' => ($obj_error instanceof \ErrorException ? 'Internal Error' : 'Exception')
        ];
        $str_log_message = get_class($obj_error) . ': ' . $obj_error->getMessage();
        if($this->bol_expose_errors) {
            $arr_response['detail'] = $str_log_message;
        }
        if($int_code < 400 || $int_code > 505) {
            $int_code = 500;
        }
        $this->sendResponse($arr_response, $int_code);
        $this->getLogger()->error("[JAPI] [{$int_code}] Error: {$str_log_message}");
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

    /**
     * Tell JAPI to expose error detail, or not!
     *
     * @param bool $bol_expose
     */
    public function exposeErrorDetail($bol_expose = true)
    {
        $this->bol_expose_errors = $bol_expose;
    }

}