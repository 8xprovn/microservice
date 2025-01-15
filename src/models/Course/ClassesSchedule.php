<?php

namespace Microservices\models\Course;

use Illuminate\Support\Arr;

class ClassesSchedule extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/course/classes_schedule';
        
    }
}
