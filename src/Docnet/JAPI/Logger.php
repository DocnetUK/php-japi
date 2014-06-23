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
 * JAPI Logger
 *
 * @author Tom Walder <tom@docnet.nu>
 */
class Logger implements Interfaces\Logger
{

    /**
     * Default PHP log levels as strings
     *
     * @var array
     */
    protected $arr_levels = array(
        LOG_EMERG => 'EMERG',
        LOG_ALERT => 'ALERT',
        LOG_CRIT => 'CRIT',
        LOG_ERR => 'ERR',
        LOG_WARNING => 'WARNING',
        LOG_NOTICE => 'NOTICE',
        LOG_INFO => 'INFO',
        LOG_DEBUG => 'DEBUG'
    );

    /**
     * Log a message
     *
     * @param $int_level
     * @param $str_message
     */
    public function log($int_level, $str_message)
    {
        if(!isset($this->arr_levels[$int_level])) {
            $int_level = LOG_ERR;
        }
        syslog($int_level, $str_message);
        error_log("[{$this->arr_levels[$int_level]}] {$str_message}");
    }

}