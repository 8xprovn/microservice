<?php

namespace Microservices\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class Retry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Create a new job instance.
     *
     * @param  array  $jobId
     * @return void
     */
    protected $data;
    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $payload = $this->job->payload();
        $data_command = $payload['data']['command'] ?? '';
        $command = unserialize($data_command, ['allowed_classes' => true]);
        $jobId = $command->serviceJob['uuid'] ?? $command->serviceJob['job_id'] ?? null;
        if (empty($jobId)) {
            preg_match('/s:\d+:"(?:uuid|job_id)";s:\d+:"([^"]+)"/',  $data_command, $matches);
            $jobId = $matches[1] ?? null;
        }
        Artisan::call('queue:retry', ['id' => [$jobId]]);
        $output = Artisan::output();

        logger()->info('Retry result', [
            'job_id' => $jobId,
            'output' => $output,
            'payload' => $command,
        ]);
    }
}
