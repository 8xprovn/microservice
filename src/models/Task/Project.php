<?php

namespace Microservices\models\Task;


class Project extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/task/projects';
    }
}
