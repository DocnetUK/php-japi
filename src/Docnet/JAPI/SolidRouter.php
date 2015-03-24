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

use Docnet\JAPI\Exceptions\Routing;

/**
 * Router for our revised "Single Action Controller" approach
 *
 * @package Docnet\App
 */
class SolidRouter
{

    /**
     * URL to route
     *
     * @var string
     */
    protected $str_url = '';

    /**
     * Output from parse_url()
     *
     * @var array|mixed
     */
    protected $arr_url = array();

    /**
     * Controller class as determined by parseController()
     *
     * @var string
     */
    protected $str_controller = '';

    /**
     * Static routes
     *
     * @var array
     */
    private $arr_static_routes = array();

    /**
     * @var string
     */
    private $str_controller_namespace = '\\';

    /**
     * We need to know the base namespace for the controller
     *
     * @param string $str_controller_namespace
     */
    public function __construct($str_controller_namespace = '\\')
    {
        $this->str_controller_namespace = $str_controller_namespace;
    }

    /**
     * Route the request.
     *
     * This means "turn the URL into a Controller (class) for execution.
     *
     * Keep URL string and parse_url array response as member vars in case we
     * want to evaluate later.
     *
     * @param string $str_url
     * @throws Routing
     */
    public function route($str_url = NULL)
    {
        $this->str_url = (NULL === $str_url ? $_SERVER['REQUEST_URI'] : $str_url);
        $this->arr_url = parse_url($this->str_url);
        if (!$this->arr_url || !isset($this->arr_url['path'])) {
            throw new Routing('URL parse error (parse_url): ' . $this->str_url);
        }
        if (!$this->routeStatic()) {
            if (!(bool)preg_match_all("#/(?<controller>[\w\-]+)#", $this->arr_url['path'], $arr_matches)) {
                throw new Routing('URL parse error (preg_match): ' . $this->str_url);
            }
            $this->setup(implode("\t", $arr_matches['controller']));
        }
    }

    /**
     * Check for static routes, setup if needed
     *
     * @return bool
     */
    protected function routeStatic()
    {
        if (isset($this->arr_static_routes[$this->arr_url['path']])) {
            $this->setup($this->arr_static_routes[$this->arr_url['path']], NULL, FALSE);
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Check & store controller from URL parts
     *
     * @param $str_controller
     * @param $bol_parse
     * @throws Routing
     */
    protected function setup($str_controller, $bol_parse = TRUE)
    {
        $this->str_controller = ($bol_parse ? $this->parseController($str_controller) : $str_controller);
        if (!method_exists($this->str_controller, 'dispatch')) {
            throw new Routing("Could not find controller: {$this->str_controller}");
        }
    }

    /**
     * Translate URL controller name into name-spaced class
     *
     * @param $str_controller
     * @return string
     */
    protected function parseController($str_controller)
    {
        return $this->str_controller_namespace . str_replace([" ", "\t"], ["", '\\'], ucwords(str_replace("-", " ", strtolower($str_controller))));
    }

    /**
     * Get the routed controller
     *
     * @return string
     */
    public function getController()
    {
        return $this->str_controller;
    }

    /**
     * Add a single static route
     *
     * @param string $str_path
     * @param string $str_controller
     * @return \Docnet\JAPI\Router
     */
    public function addRoute($str_path, $str_controller)
    {
        $this->arr_static_routes[$str_path] = $str_controller;
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

}