<?php

namespace Microservices\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;

class BusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $key;
    protected $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($key,$data)
    {
        $this->key = $key;
        $this->data = $data;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = $this->data;
        \Log::info($this->key.' - '.json_encode($data));
        $listener = $this->key;
        if (strpos($this->key, '()') !== false) {
            $arrListener = explode('\\', $listener);
            $func = end($arrListener);
            $listener = str_replace('\\'.$func, '' , $listener);
            $func = str_replace('()', '', $func);
        }
        else {
            $func = 'handle';
        }

        if (strpos($listener, 'App\Http\Controllers') !== false) {
            $data = new Request($data);
        }

        // Gọi phương thức handle của job khác
        if (class_exists($listener)) {
            return $listener::dispatch($data)->onQueue(config('app.service_code'));
        }
        
        return (new $listener())->$func($data);
    }
}
