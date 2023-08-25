<?php
namespace Microservices\Events;

use Illuminate\Events\Dispatcher;
use \BusListener;
use BusEvent;

class Event
{
    public function __construct() {
        $this->subscriber();
    }
    // public function 
    public function __call($method,$arg = []) {
        $func = '\Microservices\Events\\'.$method;        
        return event(new $func(...$arg));
    }
    public function subscriber() {
        $events = \App::make(Dispatcher::class);
        $events->listen(
            \Microservices\Events\BusEvent::class,
            [\Microservices\Listeners\BusListener::class, 'handle']
        );
    }
}