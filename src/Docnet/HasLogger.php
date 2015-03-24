<?php

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
