<?php

namespace Microservices\models\Hr;

class ShiftsCustomerCare extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/hr/setting/shifts-customer-care';
        
    }
}

