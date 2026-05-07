<?php

namespace Microservices\models\App; 

class DataRegister extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/app/data_register';
        
    }
}