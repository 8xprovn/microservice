<?php

namespace Microservices\models\System;

use Illuminate\Support\Arr;

class ScheduleLogs extends \Microservices\models\Model
{
    protected $_url;
    protected $_service_code;
    protected $_listener_update;

    //protected $is_cache = 1;
    public function __construct($options = [])
    {
        $this->_url = env('API_MICROSERVICE_URL_V2') . '/core/logs';
        $this->setToken($options['token'] ?? 'system');
        $this->_listener_update = '\App\Listeners\ScheduleLogsSubscriber\update()';
        $this->_service_code = 'erp_system_backend_v2';
    }
    
    public function update($params = array())
    {
        $input = collect($params)->only(['listener', 'uuid', 'status'])->toArray();

        ///////// VALIDATION ////////
        $validator = \Validator::make($input, [
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            \Log::error($validator->errors()->first());
            return false;
        }
        \App\Jobs\BusJob::dispatch($this->_listener_update, $input)->onQueue($this->_service_code);
        return true;
    } 
}
