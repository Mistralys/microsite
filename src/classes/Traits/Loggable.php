<?php

declare(strict_types=1);

namespace Microsite;

trait Traits_Loggable
{
    abstract public function getLogPrefix() : string;
    
    protected $logPrefix;

   /**
    * Resets the log prefix, so it is generated anew
    * the next time a log message is added: use this
    * in case the prefix contains data that can change
    * during a request.
    */
    protected function resetLogPrefix() : void
    {
        $this->logPrefix = null;
    }
    
    public function log(string $message) : void
    {
        if(!isset($this->logPrefix)) {
            $this->logPrefix = $this->getLogPrefix();
        }
        
        Logger::log($message, $this->logPrefix);
    }
    
    public function logHeader(string $header) : void
    {
        Logger::logHeader($header);
    }
}

interface Interface_Loggable
{
    public function log(string $message) : void;
    
    public function logHeader(string $header) : void;
    
    public function getLogPrefix() : string;
}