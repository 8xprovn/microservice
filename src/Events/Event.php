<?php
namespace Microservices\Events;

class Event
{
    public function __construct() {
    }
    // public function 
    public function __call($method,$arg = []) {
        $func = '\Microservices\Events\\'.$method;        
        return event(new $func(...$arg));
    }
}