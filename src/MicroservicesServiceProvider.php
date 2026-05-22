<?php

namespace Microservices;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Event;
use Microservices\models\Microservices;
use Microservices\Facade\Microservices as MicroservicesFacade;

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
        $this->actionJob();
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

    public function actionJob()
    {
        Queue::failing(function (JobFailed $event) {
            $payload = $event->job->payload();
            $jobFaileId = $event->job->getJobId();
            $inputData = $payload['data']['command'] ?? null;
            $error_message = $event->exception->getMessage();
            // Nếu không phải job do BusJob dispatch, bỏ qua.
            if (empty($inputData)) return;

            $jobInstance = unserialize($inputData);
            $jobInstance = (array) $jobInstance;

            $cleanArray = [];
            foreach ($jobInstance as $key => $value) {
                $cleanKey = preg_replace('/^\x00\*\x00/', '', $key);
                $cleanArray[$cleanKey] = $value;
            }
            if (!empty($cleanArray['data']['execution_log_id'])) {
                \Microservices::System('ExcutionsLogs')->updateAction([
                    'execution_log_id' => $cleanArray['data']['execution_log_id'] ?? '-1',
                    'status' =>  'error',
                    'status_listener' =>  'error',
                    'fail_job_id' => $jobFaileId,
                    'payload' => $cleanArray['data'] ?? [],
                    'service' => config('app.service_code'),
                    'error_message' => $error_message,
                ]);
            }
        });
    }
}
