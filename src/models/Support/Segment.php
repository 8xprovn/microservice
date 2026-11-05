<?php

namespace Microservices\models\Support;


class Segment extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/support/segments';
    }
}

D:\Projects\a-gitlab-v2\support-backend\vendor\8xprovn\microservice\src\models\Support
