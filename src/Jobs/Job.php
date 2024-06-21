<?php

namespace Microservices\Jobs;

class Job
{
    public function __construct() {
    }
    // public function
    public function __call($method,$arg = []) {
        $func = '\Microservices\Jobs\\'.$method;
        $r = dispatch(new $func(...$arg));
        //// call event local////
        if (class_exists('\App\Jobs\\'.$arg[0])) {
            $eventFunction = '\App\Jobs\\'.$arg[0];
            $dataEvent = $arg;
            unset($dataEvent[0]);
            $r = dispatch(new $eventFunction(...$dataEvent));
        } 
        return $r;
    }
}
