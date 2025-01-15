<?php

namespace Microservices\models\System;

use Illuminate\Support\Arr;

class ExportSetting extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/system/export_settings';
        
    }
}
