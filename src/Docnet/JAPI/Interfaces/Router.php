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
namespace Docnet\JAPI\Interfaces;

/**
 * JAPI Router Interface
 *
 * @author Tom Walder <tom@docnet.nu>
 */
interface Router
{

    /**
     * Route the request.
     *
     * This means "turn the URL into a Controller (class) and Action (method)
     * for execution.
     *
     * @throws Exceptions\Routing
     */
    public function route($str_url = NULL);

    /**
     * Dispatch the request
     *
     * @throws \Exception
     */
    public function dispatch();

}