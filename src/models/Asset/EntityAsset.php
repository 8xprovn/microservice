<?php

namespace Microservices\models\Asset;

use Illuminate\Support\Arr;

class EntityAsset extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/asset/entity-asset';
        $this->setToken($options['token'] ?? 'system');
    }
}

