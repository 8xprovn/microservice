<?php

namespace Microservices\models\Hr;
 
class EmployeeWorkAny extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/hr/employee_work_any';
    }
}
