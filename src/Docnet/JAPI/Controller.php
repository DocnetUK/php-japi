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
namespace Docnet\JAPI;

/**
 * Base Controller
 *
 * There's some stuff in here which feels like it should be part of a "Request"
 * object but, we'll leave it here for now!
 *
 * @author Tom Walder <tom@docnet.nu>
 * @abstract
 */
abstract class Controller
{

    /**
     * Response data
     *
     * @var null|object|array
     */
    protected $obj_response = null;

    /**
     * Request body
     * @var string
     */
    protected $str_request_body = null;

    /**
     * Request body decoded as json
     * @var string
     */
    protected $str_request_body_json = null;

    /**
     * Default, empty pre dispatch
     *
     * Usually overridden for authentication
     */
    public function preDispatch()
    {
    }

    /**
     * Default, empty post dispatch
     *
     * Available for override - perhaps for UOW DB writes?
     */
    public function postDispatch()
    {
    }

    /**
     * Was there an HTTP POST?
     *
     * Realistically, we're probably not going to use PUT, DELETE (for now)
     *
     * @return bool
     */
    protected final function isPost()
    {
        return ($_SERVER['REQUEST_METHOD'] === 'POST');
    }

    /**
     * Get the HTTP request headers
     *
     * getallheaders() available for CGI (in addition to Apache) from PHP 5.4
     *
     * Fall back to manual processing of $_SERVER if needed
     *
     * @todo Test on Google App Engine
     *
     * @return array
     */
    protected function getHeaders()
    {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }
        $arr_headers = [];
        foreach ($_SERVER as $str_key => $str_value) {
            if (strpos($str_key, 'HTTP_') === 0) {
                $arr_headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($str_key, 5)))))] = $str_value;
            }
        }
        return $arr_headers;
    }

    /**
     * Get the request body
     *
     * @return string
     */
    protected function getBody()
    {
        if ($this->str_request_body == null) {
            // We store this as prior to php5.6 this can only be read once
            $this->str_request_body = file_get_contents('php://input');
        }
        return $this->str_request_body;
    }

    /**
     * Get the request body as a JSON object
     *
     * @return mixed
     */
    protected function getJson()
    {
        if ($this->str_request_body_json === null) {
            $this->str_request_body_json = json_decode($this->str_request_body);
        }
        return $this->str_request_body_json;
    }

    /**
     * Get a request parameter. Check GET then POST data, then optionally any json body data.
     *
     * @param string $str_key
     * @param mixed $str_default
     * @param bool $check_json_body
     * @return mixed
     */
    protected function getParam($str_key, $str_default = null, $check_json_body = false)
    {
        $str_query = $this->getQuery($str_key);
        if (null !== $str_query) {
            return $str_query;
        }
        $str_post = $this->getPost($str_key);
        if (NULL !== $str_post) {
            return $str_post;
        }
        // Optionally check Json in Body
        if ($check_json_body && isset($this->getJson()->$str_key)) {
            if (null !== $this->getJson()->$str_key) {
                return $this->getJson()->$str_key;
            }
        }
        return $str_default;
    }

    /**
     * Get a Query/GET input parameter
     *
     * @param string $str_key
     * @param mixed $str_default
     * @return mixed
     */
    protected function getQuery($str_key, $str_default = NULL)
    {
        return (isset($_GET[$str_key]) ? $_GET[$str_key] : $str_default);
    }

    /**
     * Get a POST parameter
     *
     * @param string $str_key
     * @param mixed $str_default
     * @return mixed
     */
    protected function getPost($str_key, $str_default = NULL)
    {
        return (isset($_POST[$str_key]) ? $_POST[$str_key] : $str_default);
    }

    /**
     * Set the response object
     *
     * @param $obj_response
     */
    protected function setResponse($obj_response)
    {
        $this->obj_response = $obj_response;
    }

    /**
     * Get the response data
     *
     * @return object|array
     */
    public function getResponse()
    {
        return $this->obj_response;
    }

    /**
     * Main dispatch method
     *
     * @return mixed
     */
    abstract public function dispatch();

}