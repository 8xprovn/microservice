<?php

namespace Microservices\models\Inventory;

class Logs extends \Microservices\models\Model
{
    protected $_url;
//    protected $is_cache = 1;

    public function __construct($options = [])
    {
        $this->_url = env('API_MICROSERVICE_URL_V2') . '/inventory/logs';
        
    }

    public function getLogStatus($status, $params = [], $options = [])
    {
        try {
            $filter = [];
            foreach ($params as $k => $v) {
                if (is_null($v)) continue;
                switch ($k) {
                    default:
                        $filter[$k] = $v;
                        break;
                }
            }
            $q = $options;
            $q['filter'] = $filter;
            return ($result = $this->safeGet($this->_url . "-{$status}", $q, (string) $this->getToken())) === false ? [] : $result;
        }catch (\Exception $ex){
            return [];
        }
    }
}
