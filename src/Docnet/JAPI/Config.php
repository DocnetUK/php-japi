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
/**
 * JAPI Configuration
 *
 * @author Tom Walder <tom@docnet.nu>
 */
class Config
{

    /**
     * Running configuration
     *
     * @var array
     */
    private $arr_cfg = array();

    /**
     * Merge user supplied config array (if any) with our defaults
     *
     * @param array|null $arr_cfg
     */
    public function __construct($arr_cfg = NULL)
    {
        $this->arr_cfg = array_merge(array(
            'live' => TRUE,
            'controller_namespace' => '\\',
            'dispatch_loops' => 1
        ), (array)$arr_cfg);
    }

    /**
     * Are we running in a live environment?
     *
     * @return bool
     */
    public function isLive()
    {
        return ($this->arr_cfg['live'] === TRUE);
    }

    /**
     * Get a config parameter.
     *
     * Returns NULL when a parameter does not exist
     *
     * @param $str_key
     * @return mixed
     */
    public function get($str_key)
    {
        if (isset($this->arr_cfg[$str_key])) {
            return $this->arr_cfg[$str_key];
        }
        return NULL;
    }

    /**
     * Set a config parameter
     *
     * @param $str_key
     * @param $mix_val
     * @return \Docnet\JAPI\Config
     * @fluent
     */
    public function set($str_key, $mix_val)
    {
        $this->arr_cfg[$str_key] = $mix_val;
        return $this;
    }

}