<?php

namespace Microservices\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BusEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;
    public $event_name;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($event_name, $data = array())
    {
        $this->data = $data;
        $this->event_name = $event_name . '::'.config('app.service_code');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
