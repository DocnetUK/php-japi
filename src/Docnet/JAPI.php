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
            if (is_callable($controller_source)) {
                $obj_controller = $controller_source();
            } else {
                $obj_controller = $controller_source;
            }
            if($obj_controller instanceof Controller) {
                $this->dispatch($obj_controller);
            } else {
                throw new \Exception('Unable to bootstrap');
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
            $this->jsonError($arr_error['message']);
        }
    }

    /**
     * Whatever went wrong, let 'em have it in JSON
     *
     * @todo Environment or LIVE check
     *
     * @param string|\Exception $mix_message
     * @param int $int_code
     */
    protected function jsonError($mix_message = NULL, $int_code = 500)
    {
        $int_code = (int)$int_code;
        http_response_code($int_code);
        $arr_response = [
            'code' => $int_code,
            'msg' => 'Internal error'
        ];
        if ($mix_message instanceof \Exception) {
            $arr_response['msg'] = 'Exception';
            $str_log_message = get_class($mix_message) . ': ' . $mix_message->getMessage();
            if(TRUE) { // @todo Environment or LIVE check
                $arr_response['detail'] = $str_log_message;
            }
        } elseif (is_string($mix_message)) {
            $str_log_message = $mix_message;
        } else {
            $str_log_message = '';
        }
        $this->sendResponse($arr_response);
        $this->getLogger()->error("[JAPI exiting with {$int_code}] " . $str_log_message);
    }

    /**
     * Output the response
     *
     * @param $response
     */
    protected function sendResponse($response)
    {
        header('Content-type: application/json');
        echo json_encode($response);
    }

}