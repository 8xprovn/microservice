<?php

namespace Microservices\models\App;

use Illuminate\Support\Arr;

class News extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/app/news';
        
    }

    public function count($params = [], $options = [])
    {
        $filter = [];
        foreach($params as $k => $v){
            if (is_null($v)) continue;
            switch ($k) {
                default:
                    $filter[$k] = $v;
                    break;
            }
        }
        $q = $options;
        $q['filter'] = $filter;
        $result = $this->safeGet($this->_url . '/count', $q, (string) $this->getToken());
        return $result === false ? false : $result;
    }
}
