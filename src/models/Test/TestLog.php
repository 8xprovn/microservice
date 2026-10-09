<?php

namespace Microservices\models\Test;

use Illuminate\Support\Arr;

class TestLog extends \Microservices\models\Model
{
    protected $_url;
    protected $is_cache = 1;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/tests/test-logs';
        
    }

    public function createLog($params) {
        $params = \Arr::whereNotNull($params);
        if (!empty($this->only['create'])) {
            $params = \Arr::only($params, $this->only['create']);
        }
        if (!empty($this->dataDefault['create'])) {
            $params = array_merge($this->dataDefault['create'], $params);
        }
        if (!empty($this->idAutoIncrement) && empty($params[$this->primaryKey])) {
            $params[$this->primaryKey] = $this->getNextSequence($this->table);
        }
        $params['created_time'] = time();
        $url = $this->_url . "/create-log";
        $result = $this->safePost($url, $params, (string) $this->getToken());
        return $result === false ? false : $result;
    }

    public function updateLog($params) {
        $params = \Arr::whereNotNull($params);
        if (!empty($this->only['create'])) {
            $params = \Arr::only($params, $this->only['create']);
        }
        if (!empty($this->dataDefault['create'])) {
            $params = array_merge($this->dataDefault['create'], $params);
        }
        if (!empty($this->idAutoIncrement) && empty($params[$this->primaryKey])) {
            $params[$this->primaryKey] = $this->getNextSequence($this->table);
        }
        $params['created_time'] = time();
        $url = $this->_url . "/update-log";
        $result = $this->safePost($url, $params, (string) $this->getToken());
        return $result === false ? false : $result;
    }
}
