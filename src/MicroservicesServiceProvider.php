<?php

namespace Microservices;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Event;
use Microservices\models\Microservices;

use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Log;
use Microservices\Facade\Microservices as MicroservicesFacade;
use Microservices\models\System\Excutions;
use Microservices\models\System\ExcutionsLogs;

class MicroservicesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // User Provider
        Event::listen(
            \Microservices\Events\BusEvent::class,
            [\Microservices\Listeners\BusListener::class, 'handle']
        );
        Event::listen(
            \Microservices\Events\FlowEvent::class,
            [\Microservices\Listeners\FlowListener::class, 'handle']
        );

        $loader = AliasLoader::getInstance();
        $loader->alias('Microservices', MicroservicesFacade::class);

        $this->register_event();
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Facades
        $this->app->singleton('Microservices', function ($app) {
            return $app->make(Microservices::class);
        });
        //$this->app->alias('Microservices',MicroservicesFacade::class);
    }


    public function register_event(){
        Queue::after(function (JobProcessed $event) {
            $payload = $event->job->payload();
            $jobId = $event->job->getJobId();
            $inputData = $payload['data']['command']; // Đây là chuỗi đã được serialize của job
            // Khôi phục lại đối tượng job từ chuỗi serialize
            $jobInstance = unserialize($inputData);
            $jobInstance = (array) $jobInstance;

            $cleanArray = [];
            foreach ($jobInstance as $key => $value) {
                $cleanKey = preg_replace('/^\x00\*\x00/', '', $key);
                $cleanArray[$cleanKey] = $value;
            }
            if(!empty($cleanArray['data']['execution_log_id'])){
                $_data = [
                    'listener' => $cleanArray['key'] ?? '',
                    'execution_log_id' => $cleanArray['data']['execution_log_id'] ?? '-1',
                    'status' => "success",
                    'jobId' => $jobId,
                    'service' => config('app.service_code'),
                ]; 
                \App::make(ExcutionsLogs::class)->updateAction($_data); 
            }
            
            return;
        });

        Queue::failing(function (JobFailed $event) {
            // Job bị thất bại
            \Log::error('Job thất bại: ' . $event->job->getName());
            // Lấy thông tin đầu vào của job
            $payload = $event->job->payload(); 
            $jobId = $event->job->getJobId();
            $inputData = $payload['data']['command']; // Đây là chuỗi đã được serialize của job
            // Khôi phục lại đối tượng job từ chuỗi serialize
            $jobInstance = unserialize($inputData);
            $jobInstance = (array) $jobInstance;

            $cleanArray = [];
            foreach ($jobInstance as $key => $value) {
                $cleanKey = preg_replace('/^\x00\*\x00/', '', $key);
                $cleanArray[$cleanKey] = $value;
            }

            if(!empty($cleanArray['data']['execution_log_id'])){
                $_data = [
                    'listener' => $cleanArray['key'] ?? '',
                    'execution_log_id' => $cleanArray['data']['execution_log_id'] ?? '-1',
                    'status' => "error",
                    'jobId' => $jobId,
                    'payload' => $cleanArray['data'] ?? [],
                    'service' => config('app.service_code'),
                    "error_message" => $event->exception->getMessage(),
                ];
                \App::make(ExcutionsLogs::class)->updateAction($_data); 
            }
        });
    }
}
