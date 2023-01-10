<?php

namespace Models\Org;

use Illuminate\Support\Arr;

class Branchs extends \Models\Model
{
    protected $_url;
    public function __construct() {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/call-center';
    }
}
