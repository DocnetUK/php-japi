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

/**
 * Base Test Class
 *
 * @author Tom Walder <tom@docnet.nu>
 * @abstract
 */
abstract class BaseTest
{
    private $flt_start = 0.0;

    private $int_passes = 0;

    private $int_failures = 0;

    public function __construct()
    {
        $this->flt_start = microtime(TRUE);
        $this->say("");
        $this->say("-------------------------------------");
        $this->say("Starting at       " . date('Y-m-d H:i:s'));
        $this->say("");
    }

    public function run($str_test = NULL)
    {
        if (NULL === $str_test) {
            $arr_tests = array();
            $obj_reflection = new ReflectionObject($this);
            foreach ($obj_reflection->getMethods() as $obj_method) {
                if (substr($obj_method->name, 0, 4) == 'test') {
                    $arr_tests[] = substr($obj_method->name, 4);
                }
            }
        } else {
            $arr_tests = array($str_test);
        }

        foreach ($arr_tests as $str_test) {
            $this->say(str_pad("- {$str_test}", 33, " "), FALSE);
            try {
                call_user_func(array($this, 'test' . $str_test));
                $this->pass();
            } catch (\InvalidArgumentException $obj_ex) {
                $this->fail();
                $this->say("  " . $obj_ex->getMessage());
            }
        }
    }

    protected function pass()
    {
        $this->int_passes++;
        $this->say('PASS');
    }

    protected function fail()
    {
        $this->int_failures++;
        $this->say('FAIL');
    }

    private function say($str, $bol_eol = TRUE)
    {
        echo $str . ($bol_eol ? PHP_EOL : '');
    }

    public function __destruct()
    {
        $this->say("");
        $this->say("Passes   : " . $this->int_passes);
        $this->say("Failures : " . $this->int_failures);
        $this->say("");
        $this->say("Time taken: " . round(microtime(TRUE) - $this->flt_start, 4) . "s");
        $this->say("Complete at       " . date('Y-m-d H:i:s'));
        $this->say("-------------------------------------");
        $this->say("");
    }

    /**
     * Validate parameter is an array
     *
     * Optionally compare array contents
     *
     * @param $arr_test
     * @param null $arr_compare
     * @throws InvalidArgumentException
     */
    protected function assertIsArray($arr_test, $arr_compare = NULL)
    {
        if(is_array($arr_test)) {
            if(NULL !== $arr_compare) {
                if(count($arr_test) !== count($arr_compare)) {
                    throw new InvalidArgumentException("Array counts do not match: [".count($arr_test)."] vs [".count($arr_compare)."]");
                }
                foreach($arr_test as $mix_key => $mix_val) {
                    // $this->say("comparing ($mix_key) [{$arr_test[$mix_key]}] == [$arr_compare[$mix_key]]");
                    if($arr_test[$mix_key] != $arr_compare[$mix_key]) {
                        throw new InvalidArgumentException("Array values do not match: [$arr_test[$mix_key]] vs [$arr_compare[$mix_key]]");
                    }
                }
            }
        } else {
            throw new InvalidArgumentException("Input not an array");
        }
    }
}