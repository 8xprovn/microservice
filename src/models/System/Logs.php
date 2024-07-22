<?php

namespace Microservices\models\System;

use Illuminate\Support\Arr;

class Logs extends \Microservices\models\Model
{
    protected $_url;
    protected $_service_code;
    protected $_listener_file;

    //protected $is_cache = 1;
    public function __construct($options = [])
    {
        $this->_url = env('API_MICROSERVICE_URL_V2') . '/core/logs';
        $this->setToken($options['token'] ?? 'system');
        $this->_listener_file = '\App\Listeners\LogsSubscriber\store()';
        $this->_service_code = 'erp_system_backend_v2';
    }

    public function pushLogs($params = array())
    {
        $input = collect($params)->only(['relate_type', 'relate_id', 'data_news', 'data_olds', 'created_by', 'action', 'service', 'time_update'])->toArray();
        if (empty($input['service'])) $input['service'] = config('app.service_code');
        ///////// VALIDATION ////////
        $validator = \Validator::make($input, [
            'service' => 'required',
            'relate_type' => 'required',
            'relate_id' => 'required',
            'action' => 'required',
            'created_by' => 'required',
        ]);
        if ($validator->fails()) {
            \Log::error($validator->errors()->first());
            return false;
        }

        \App\Jobs\BusJob::dispatch($this->_listener_file, $input)->onQueue($this->_service_code);
        return true;
    }

}
