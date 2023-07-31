<?php

namespace Microservices\models\Hr;

use Illuminate\Support\Arr;

class Device extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/hr/device';
        $this->setToken($options['token'] ?? 'system');
    }

    public function storeFirst(array $params)
    {
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
        $url = env('API_MICROSERVICE_URL_V2').'/hr/device/store-first';
        $response = \Http::acceptJson()->withToken($this->access_token)->POST($url, $params);
        if ($response->successful()) {
            return $response->json();
        } 
        \Log::error($url . $response->body());
        return false;
    }
}
