<?php

namespace Microservices\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class RetryFailedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $jobId;
    public $serviceJob;

    /**
     * Create a new job instance.
     *
     * @param  array  $jobId
     * @return void
     */
    public function __construct($jobId, $serviceJob = '')
    {
        $this->jobId = $jobId;
        $this->serviceJob = !empty($serviceJob) ? $serviceJob : config('app.service_code');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Artisan::call('queue:retry', ['id' => [$this->jobId]]);
            $output = Artisan::output();

            if (preg_match('/FAIL|ERROR/i', $output)) {
                $status = 'retry_failed';
                $message = $output;
            } elseif (preg_match('/DONE|Retried|SUCCESS/i', $output)) {
                $status = 'retry_successed';
            } else {
                $status = 'retry_failed';
                $message = $output;
            }
        } catch (\Exception $e) {
            Log::error("Failed to retry job ID: {$this->jobId}. Error: {$e->getMessage()}");
            $message = $e->getMessage();
            $status = 'retry_failed';
        } finally {
            // Update the status of the failed job in the database
            \Microservices\Jobs\BusJob::dispatch(
                'App\Jobs\UpdateFailedJob',
                [
                    'job_id' => $this->jobId,
                    'service' => $this->serviceJob,
                    'status' => $status,
                    'message' => $message ?? '',
                ]
            )->onQueue('erp_system_backend_v2');
        }
    }
}
