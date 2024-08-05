<?php

namespace Microservices\Jobs;


use App\Models\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Microservices\models\System\Logs;

class Export implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $key;
    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($key, $data)
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
        \Log::info($this->key . ' - ' . json_encode($data));

        // Tạo log
        $relate_type = str_replace('\App\Jobs\\', '', $this->key);
        $relate_type = str_replace('\\', ' ', $relate_type);
        // (new Logs())->create(array_merge($data, ['service' => config('app.service_code'), 'relate_type' => \Str::slug($relate_type), 'relate_id' => 0, 'action' => 'export']));

        $listener = $this->key;
        if (strpos($this->key, '()') !== false) {
            $arrListener = explode('\\', $listener);
            $func = end($arrListener);
            $listener = str_replace('\\' . $func, '', $listener);
            $func = str_replace('()', '', $func);
        } else {
            $func = 'handle';
        }
        // Gọi phương thức handle của job khác
        if (class_exists($listener)) {
            return $listener::dispatch($data)->onQueue(config('app.service_code'));
        }
        return (new $listener())->$func($data);
    }
}
