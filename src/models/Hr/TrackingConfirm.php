<?php

namespace Microservices\models\Hr;

use Illuminate\Support\Arr;

class TrackingConfirm extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/hr/tracking_confirm';
        
    }

    public function closeMonthly($params = [], $options = [])
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
        $result = $this->safeGet($this->_url . "/close_monthly", $q, (string) $this->getToken());
        return $result === false ? false : $result;
    }
}
