<?php

namespace Microservices\models;

class Microservices
{
    private $eventInstance;
    private $jobInstance;
    private $jobExport;

    public function loadCache($classLoad, $arg = [])
    {
        $className = '\Microservices\Caches\\' . $classLoad;
        return new $className($arg);
    }

    public function event()
    {
        if (!$this->eventInstance) {
            $this->eventInstance = new \Microservices\Events\Event;
        }
        return $this->eventInstance;
    }

    public function job()
    {
        if (!$this->jobInstance) {
            $this->jobInstance = new \Microservices\Jobs\Job;
        }
        return $this->jobInstance;
    }

    public function export()
    {
        if (!$this->jobExport) {
            $this->jobExport = new \Microservices\Exports\Export();
        }
        return $this->jobExport;
    }

    // public function
    public function __call($method, $arg = [])
    {
        $func = array_shift($arg);
        $className = '\Microservices\models\\' . $method . '\\' . $func;
        return new $className(...$arg);
    }
}
