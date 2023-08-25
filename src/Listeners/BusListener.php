<?php

namespace Microservices\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BusListener implements ShouldQueue
{
    public $queue = 'event_bus';
    private $busInstance = '';
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ExampleEvent  $event
     * @return void
     */
    public function handle($event)
    {
        if (!$this->busInstance) {
            $this->busInstance = new \App\Listeners\BusListener;
        }
        return $this->busInstance->handle($event);
    }
}
