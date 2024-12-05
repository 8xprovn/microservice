<?php

namespace Microservices\models\System;

use Illuminate\Support\Arr;

class Excutions extends \Microservices\models\Model
{
    protected $_url;
    protected $_service_code;
    protected $_listener;

    //protected $is_cache = 1;
    public function __construct($options = [])
    {
        $this->_url = env('API_MICROSERVICE_URL_V2') . '/core/workflows/excutions';
        $this->setToken($options['token'] ?? 'system');
        $this->_listener = 'App\Jobs\Execution';
        $this->_service_code = 'erp_system_backend_v3';
    }

    public function updateAction($params = array())
    {
        if (!empty($params['execution_id'])) {
            return \Microservices\Jobs\BusJob::dispatch($this->_listener, $params)->onQueue($this->_service_code);
        }
    }
}
