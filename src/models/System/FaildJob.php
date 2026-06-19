<?php

namespace Microservices\models\System;

use Illuminate\Support\Arr;

class FaildJob
{
    protected $_service_code;
    protected $_job;
    public function __construct($options = [])
    {
        $this->_job = '\App\Jobs\FailedJob';
        $this->_service_code = 'erp_system_backend_v3';
    }

    public function asyncFailedJob($params = array(), $status = 'failed')
    {
        $params['status'] = $status;
        $params['_ignore_monitor'] = true;
        return \Microservices\Jobs\BusJob::dispatch($this->_job, $params)->onQueue($this->_service_code);
    }
}
