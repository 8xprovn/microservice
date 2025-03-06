<?php

namespace Microservices\models\Recruitment;

use Illuminate\Support\Arr;

class JobCategory extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/recruitment/job_category';
        
    }
}
