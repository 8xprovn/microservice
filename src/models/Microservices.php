<?php

namespace Microservices\models;

class Microservices
{
    private $eventInstance;
    private $jobInstance;
    private $jobExport;

    public function loadCache($classLoad)
    {
        $className = 'Microservices\Caches\\' . $classLoad;
        app()->singletonIf($className, function ($app)  use($className) {
            return new $className();
        });
        return app($className);
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
        if (empty($func)) {
            $className = 'App\Models\\' . $method;
        }
        else {
            $className = 'Microservices\models\\' . $method . '\\' . $func;
        }
        app()->singletonIf($className, function ($app)  use($className) {
            return new $className();
        });
        return app($className);
    }
}
