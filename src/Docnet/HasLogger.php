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

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * HasLogger Trait
 */
trait HasLogger
{
    /**
     * @var LoggerInterface
     */
    protected $logger = NULL;

    /**
     * Sets a logger.
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Gets a logger.
     *
     * @return LoggerInterface
     */
    protected function getLogger()
    {
        if(NULL === $this->logger) {
            $this->logger = new NullLogger();
        }
        return $this->logger;
    }
}
