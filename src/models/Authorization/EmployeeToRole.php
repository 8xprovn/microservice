<?php

namespace Microservices\models\Authorization;

class EmployeeToRole extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/authorization/employee';
        $this->setToken($options['token'] ?? 'system');
    }
    public function me($params)
    {
        $userId = \Auth::id();
        if (!$userId) {
            return false;
        }
        ////// GET FROM CACHE ////////
        $permission = $this->cache()->getMe($userId,$params);
        if ($permission) {
            return $permission;
        }
        ////// MISS CACHE //////////
        $url = $this->_url.'/me';
        $response = \Http::withToken($this->person_token)->get($url,$params);
        
        if ($response->successful()) {
            return $response->json();
        } 
        \Log::error($url . $response->body());
        return false;
    }
}

