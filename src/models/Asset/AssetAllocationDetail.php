<?php

namespace Microservices\models\Asset;

use Illuminate\Support\Arr;

class AssetAllocationDetail extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/asset/asset-allocation-detail';
        $this->setToken($options['token'] ?? 'system');
    }
}

