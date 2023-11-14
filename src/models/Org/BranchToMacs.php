<?php

namespace Microservices\models\Org;

use Illuminate\Support\Arr;

class BranchToMacs extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/org/branch_to_mac';
        $this->setToken($options['token'] ?? 'system');
    }
}
