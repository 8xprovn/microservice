<?php

namespace Microservices\Jobs;

use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;

class QueueMonitor
{
    /**
     * Job do monitor / retry tự dispatch — bỏ qua listener để tránh loop.
     * Lưu ý: skip chỉ ngăn Queue::after/failing gọi lại handleSuccess/handleFailed,
     * KHÔNG ngăn job chạy handle() của chính nó.
     */
    protected static array $ignoredJobs = [
        'App\Jobs\FailedJob',
        'App\Jobs\BusJob',
        'App\Listeners\BusListener',
        'Microservices\Jobs\Retry',
        'Microservices\Jobs\Job',
        'Microservices\Jobs\BusJob',
    ];

    public static function register(): void
    {
        self::listenFailing();
        self::listenProcessed();
    }

    protected static function listenFailing(): void
    {
        Queue::failing(function (JobFailed $event) {
            try {
                $data = self::extractJobData($event);
                if (self::shouldSkipMonitor($event, $data)) return;
                if (empty($data)) return;
                self::handleFailed($data, $event);
            } catch (\Throwable $e) {
                logger()->error('Queue failing listener error', [
                    'message' => $e->getMessage(),
                ]);
            }
        });
    }

    protected static function listenProcessed(): void
    {
        Queue::after(function (JobProcessed $event) {
            try {
                $data = self::extractJobData($event);
                if (self::shouldSkipMonitor($event, $data)) return;
                self::handleSuccess($data, $event);
            } catch (\Throwable $e) {
                logger()->error('Queue processed listener error', [
                    'message' => $e->getMessage(),
                ]);
            }
        });
    }

    protected static function shouldSkipMonitor(JobFailed|JobProcessed $event, array $data): bool
    {
        $innerKey = trim($data['key'] ?? '', '\\');
        if (!empty($data['data']['_ignore_monitor'])) return true;
        if ($innerKey && in_array($innerKey, self::$ignoredJobs, true)) return true;
        return false;
    }

    protected static function resolveJobClass(JobFailed|JobProcessed $event): ?string
    {
        $payload = $event->job->payload();
        return data_get($payload, 'data.commandName')  ?? data_get($payload, 'displayName');
    }

    protected static function extractJobData($event): array
    {
        $payload = $event->job->payload();
        $inputData = $payload['data']['command'] ?? null;
        if (empty($inputData)) return [];
        $jobInstance = (array) unserialize($inputData);
        $cleanArray = [];
        foreach ($jobInstance as $key => $value) {
            $cleanKey = preg_replace('/^\x00\*\x00/', '', $key);
            $cleanArray[$cleanKey] = $value;
        }
        return $cleanArray;
    }

    protected static function handleFailed(array $data, JobFailed $event): void
    {
        $jobFailId = $event->job->getJobId();
        $errorMessage = $event->exception->getMessage();
        $payload = $event->job->payload();
        preg_match('/key";s:\d+:"([^"]+)"/', $payload['data']['command'] ?? '', $matches);
        $params = [
            'status' => 'error',
            'fail_job_id' => $jobFailId,
            'uuid' => $data['data']['uuid'] ?? '',
            'payload' => $data['data'] ?? [],
            'class' => $matches[1] ?? null,
            'service' => config('app.service_code'),
            'error_message' => $errorMessage,
        ];

        if (!empty($data['data']['execution_log_id'])) {
            $params['execution_log_id'] = $data['data']['execution_log_id'];
            $params['status_listener'] = 'error';
            \Microservices::System('ExcutionsLogs')->updateAction($params);
        }
        $params['uuid'] = $payload['uuid'];
        \Microservices::System('FaildJob')->asyncFailedJob($params);
    }

    protected static function handleSuccess(array $data, JobProcessed $event): void
    {
        $payload = $event->job->payload();
        if (!empty($payload['uuid'])) \Microservices::System('FaildJob')->asyncFailedJob(['uuid' => $payload['uuid']],  'success');
    }
}
