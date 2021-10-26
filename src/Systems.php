<?php
namespace Microservices;

class Systems
{
    protected $_service_code;
    protected $_listener_file;

    public function __construct() {
        $this->_listener_file = '\App\Listeners\LogsSubscriber\store()';
        $this->_service_code = 'erp_system_backend';
    }

    public function pushLogs($params = array())
    {
        $input = \Arr::only($params, ['relate_type','relate_id','created_by','type','old_data','data']);
        ///////// VALIDATION ////////
        $validator = \Validator::make($input, [
            'relate_type' => 'required',
            'relate_id' => 'required',
            'created_by' => 'required',
            'type' => 'required',
            'old_data' => 'required_without:data',
            'data' => 'required_without:old_data',
        ]);
        if ($validator->fails()) {
            \Log::error($validator->errors()->first());
            return false;
        }
        \App\Jobs\BusJob::dispatch($this->_listener_file, $input)->onQueue($this->_service_code);
        return true;
    }
}