<?php
namespace Microservices\Events;

class Event
{
    public function __construct() {
    }
    // public function 
    public function __call($method,$arg = []) {
        $func = '\Microservices\Events\\'.$method;
        $r = event(new $func(...$arg));
        //// call event local////  
        if (class_exists('\App\Events\\'.$arg[0])) {
            $eventFunction = '\App\Events\\'.$arg[0];
            $dataEvent = $arg;
            unset($dataEvent[0]);
            $r = event(new $eventFunction(...$dataEvent));
        }
        return $r;
    }
}