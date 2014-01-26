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
namespace Docnet\JAPI;

use Docnet\JAPI;

/**
 * Router, Dispatcher
 *
 * My interpretation and implementation of Router pattern involves the dispatch
 * cycle as well. Shoot me.
 *
 * @author Tom Walder <tom@docnet.nu>
 */
class Router implements Interfaces\Router
{

    /**
     * URL to route
     *
     * @var string
     */
    private $str_url = '';

    /**
     * Output from parse_url()
     *
     * @var array|mixed
     */
    private $arr_url = array();

    /**
     * Number of dispatch loops
     *
     * @var int
     */
    private $int_dispatch_count = 0;

    /**
     * Controller class as determined by parseController()
     *
     * @var string
     */
    private $str_controller = '';

    /**
     * Action method as determined by parseAction()
     *
     * @var string
     */
    private $str_action = '';

    /**
     * Static routes
     *
     * @var array
     */
    private $arr_static_routes = array();

    /**
     * Route the request.
     *
     * This means "turn the URL into a Controller (class) and Action (method)
     * for execution.
     *
     * Keep URL string and parse_url array response as member vars in case we
     * want to evaluate later.
     *
     * @throws Exceptions\Routing
     */
    public function route($str_url = NULL)
    {
        $this->str_url = (NULL === $str_url ? $_SERVER['REQUEST_URI'] : $str_url);
        $this->arr_url = parse_url($this->str_url);
        if (!$this->arr_url || !isset($this->arr_url['path'])) {
            throw new Exceptions\Routing('URL parse error (parse_url): ' . $this->str_url);
        }
        if (!$this->static_route()) {
            if (!(bool)preg_match("#/(?<controller>[\w\-]+)/(?<action>[\w\-]+)#", $this->arr_url['path'], $arr_matches)) {
                throw new Exceptions\Routing('URL parse error (preg_match): ' . $this->str_url);
            }
            $this->setup($arr_matches['controller'], $arr_matches['action']);
        }
    }

    /**
     * Check for static routes, setup if needed
     *
     * @return bool
     */
    private function static_route()
    {
        if (isset($this->arr_static_routes[$this->arr_url['path']])) {
            $this->setup(
                $this->arr_static_routes[$this->arr_url['path']][0],
                $this->arr_static_routes[$this->arr_url['path']][1],
                FALSE
            );
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Add a single static route
     *
     * @param string $str_path
     * @param string $str_controller
     * @param string $str_action
     * @return \Docnet\JAPI\Router
     */
    public function addRoute($str_path, $str_controller, $str_action)
    {
        $this->arr_static_routes[$str_path] = array($str_controller, $str_action);
        return $this;
    }

    /**
     * Set the static routes
     *
     * @param array $arr_routes
     * @return \Docnet\JAPI\Router
     */
    public function setRoutes(Array $arr_routes)
    {
        $this->arr_static_routes = $arr_routes;
        return $this;
    }

    /**
     * Check & store controller and action from URL parts
     *
     * @param $str_controller
     * @param $str_action
     * @param $bol_parse
     * @throws Exceptions\Routing
     */
    private function setup($str_controller, $str_action, $bol_parse = TRUE)
    {
        $this->str_controller = ($bol_parse ? $this->parseController($str_controller) : $str_controller);
        $this->str_action = ($bol_parse ? $this->parseAction($str_action) : $str_action);
        if (!method_exists($this->str_controller, $this->str_action)) {
            throw new Exceptions\Routing("Could not find controller/action pair");
        }
    }

    /**
     * Translate URL controller name into namespaced class
     *
     * @param $str_controller
     * @return string
     */
    private function parseController($str_controller)
    {
        return JAPI::getConfig()->get('controller_namespace') . str_replace(" ", "", ucwords(str_replace("-", " ", $str_controller)));
    }

    /**
     * Translate URL action name into method
     *
     * @param $str_action
     * @return string
     */
    private function parseAction($str_action)
    {
        return lcfirst(str_replace(" ", "", ucwords(str_replace("-", " ", $str_action)))) . 'Action';
    }

    /**
     * Dispatch the request
     *
     * @todo Verify dispatch loop count and re-dispatch
     *
     * @throws \Exception
     */
    public function dispatch()
    {
        $this->int_dispatch_count++;
        try {
            $obj_controller = new $this->str_controller();
            $obj_controller->preDispatch();
            call_user_func(array($obj_controller, $this->str_action));
            $obj_controller->postDispatch();
            $obj_controller->jsonResponse();
        } catch (\Exception $obj_ex) {
            throw $obj_ex;
        }
    }

}