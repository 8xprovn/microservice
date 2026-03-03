<?php

namespace Microservices;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Queue\Events\JobFailed; 
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
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
            $jobName = $event->job->resolveName(); 
            // ❌ Tránh loop
            $shortName = class_basename($jobName);

            if (in_array($shortName, ['BusJob', 'FailedJob', 'RetryFailedJob'])) {
                return;
            }

            \Microservices\Jobs\BusJob::dispatch(
                'App\Jobs\FailedJob',
                [
                    'job_id' => $event->job->getJobId(),
                    'job_name' => $jobName,
                    'service' => config('app.service_code'),
                    'queue' => $event->job->getQueue(),
                    'type' => 'failed',
                    'error_message' => $event->exception->getMessage(),
                ]
            )->onQueue('erp_system_backend_v2');
        }); 
    }
}
