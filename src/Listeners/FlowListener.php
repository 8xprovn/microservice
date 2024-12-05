<?php

namespace Microservices\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
class FlowListener implements ShouldQueue
{
    public $queue = 'event_bus_v2';
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
            $this->busInstance = new \App\Listeners\FlowListener();
        }
        return $this->busInstance->handle($event);
    }
}
