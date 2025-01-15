<?php

namespace Microservices\models\System;

class Exports extends \Microservices\models\Model
{
    protected $_url;
    protected $_service_code;
    protected $_listener_file;

    //protected $is_cache = 1;
    public function __construct($options = [])
    {
        $this->_url = env('API_MICROSERVICE_URL_V2') . '/core/export_logs';
        
        $this->_listener_file = '\App\Listeners\LogsSubscriber\store()';
        $this->_service_code = 'erp_system_backend_v2';
    }
}