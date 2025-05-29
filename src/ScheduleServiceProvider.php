<?php

namespace Microservices;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class ScheduleServiceProvider extends ServiceProvider
{
    public static function create($data)
    {
        if (class_exists(\App\Models\TaskScheduling::class)) {
            $scheduleModel = new \App\Models\TaskScheduling();
        } else {
            \Log::error("Class TaskScheduling does not exist.");
            return false;
        }
        if (is_object($data)) {
            $data = $data->data;
        }
        $input = \Arr::only($data, ['relate_type', 'relate_id', 'schedule_name','type','schedule_time']);
        $validator = \Validator::make($input, [ 
            'relate_type' => 'required',
            'relate_id' => 'required',
            'schedule_name' => 'required',
            'type' => 'required',
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return false;
        }
        $check = $scheduleModel->all(['relate_type' => $input['relate_type'], 'relate_id' => $input['relate_id']]);
        if($check->isEmpty()) {
            if (empty($input['schedule_time'])) {
                $input['schedule_time'] = time();
            }
            $input['schedule_time'] = (int) $input['schedule_time'];
            $scheduleModel->create($input);
        }
        return true;
    }
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);

            $schedule->call(function () {
                if (!class_exists(\App\Models\TaskScheduling::class)) {
                    \Log::error("Class TaskScheduling does not exist.");
                    return;
                }

                $model = new \App\Models\TaskScheduling();

                $now = time();
                $data = $model->all(['schedule_time' => ['lte' => $now]], ['limit' => 1000]);

                foreach ($data as $task) {
                    if (!empty($task['type']) && !empty($task['schedule_name'])) {
                        try {
                            $micro = new \Microservices\models\Microservices();
                            if ($task['type'] === 'event') {
                                $event = $task['schedule_name'] ?? '';
                                if (!empty($event)) {
                                    $micro->event()->BusEvent($event, $task);
                                }
                            } elseif ($task['type'] === 'job') {
                                $jobClass = $task['schedule_name'] ?? '';
                                if (!empty($jobClass)) {
                                    $micro->job()->BusJob($jobClass, $task)->onQueue(config('app.service_code'));
                                }
                            }

                            // Sau khi xử lý xong, xóa task
                            $model->delete($task['_id']);
                        } catch (\Exception $e) {
                            \Log::error('Error running scheduled task', [
                                'scheduele' => $task['_id'] ?? '',
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                        }
                    }
                }
            })->everyFiveMinutes();
        });
    }


    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
       //
    }

    
}
