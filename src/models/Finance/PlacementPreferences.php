<?php

namespace Microservices\models\Finance;

use Illuminate\Support\Arr;

class PlacementPreferences extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/finance/placement-preferences';
        
    }
}