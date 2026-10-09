<?php

namespace Microservices\models\Org;

use Illuminate\Support\Arr;

class Branchs extends \Microservices\models\Model
{
    protected $_url;
    protected $is_cache = 1;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/org/branch';
        
    }

    public function location($params = [], $options = [])
    {
        $filter = [];
        foreach ($params as $k => $v) {
            if (is_null($v)) {
                continue;
            }
            switch ($k) {
                default:
                    $filter[$k] = $v;
                    break;
            }
        }
        $q = $options;
        $q['filter'] = $filter;
        $result = $this->safeGet(env('API_MICROSERVICE_URL_V2') . '/org/branch/location', $q, (string) $this->getToken());
        return $result === false ? false : $result;
    }
}
