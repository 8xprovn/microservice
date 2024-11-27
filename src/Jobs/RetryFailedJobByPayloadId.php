<?php

namespace Microservices\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RetryFailedJobByPayloadId implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $payloadId;

    /**
     * Create a new job instance.
     *
     * @param  array  $payloadId
     * @return void
     */
    public function __construct($payloadId)
    {
        $this->payloadId = $payloadId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Lấy các failed jobs chứa ID trong payload
        $failedJobs = DB::connection('mongodb') // Kết nối với MongoDB
            ->collection('failed_jobs')        // Tên collection failed_jobs
            ->where('payload', 'LIKE', '%"id":"' . $this->payloadId . '"%')
            ->get();

        if ($failedJobs->isEmpty()) {
            Log::info("No failed jobs found with payload ID: {$this->payloadId}");
            return;
        }

        foreach ($failedJobs as $failedJob) {
            $this->retryJob($failedJob);
        }
    }

    /**
     * Retry a specific failed job.
     *
     * @param object $failedJob
     * @return void
     */


    protected function retryJob($failedJob)
    {
        $job_id = (string)$failedJob['_id']; // Đảm bảo ID là string
        try {

            // Lấy job từ bảng failed_jobs
            $payload = json_decode($failedJob['payload'], 1);
            // Unserialize command từ payload
            if (!isset($payload['data']['command'])) {
                Log::error("Payload does not contain a valid command for Job ID: {$job_id}");
                return;
            }
            $jobInstance = unserialize($payload['data']['command']);
            dispatch($jobInstance);
            DB::connection('mongodb')->collection('failed_jobs')->where('_id', $job_id)->delete();
            Log::info("Successfully retried job ID: {$job_id} with payload ID: {$this->payloadId}");
        } catch (\Exception $e) {
            Log::error("Failed to retry job ID: {$job_id}. Error: {$e->getMessage()}");
        }
    }
}
