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
        $response = \Http::acceptJson()->withToken($this->getToken())->get($url,$params);
        
        if ($response->successful()) {
            return $response->json();
        } 
        \Log::error($url . $response->body());
        return false;
    }
    public function employee($params)
    {
        if (empty($params['user_id'])) {
            return false;
        }
        ////// GET FROM CACHE ////////
        $permission = $this->cache()->getMe($params['user_id'],$params);
        if ($permission) {
            return $permission;
        }
        ////// MISS CACHE //////////
        $url = $this->_url.'/detail';
        $response = \Http::acceptJson()->withToken($this->getToken())->get($url,$params);
        
        if ($response->successful()) {
            return $response->json();
        } 
        \Log::error($url . $response->body());
        return false;
    }
    public function getAllPermission($params) {
        ////// GET FROM CACHE ////////
        $permission = $this->cache()->getAllPermission($params);
        if ($permission) {
            return $permission;
        }
        ////// MISS CACHE //////////
        $url = $this->_url.'/permission';
        $response = \Http::acceptJson()->withToken($this->getToken())->get($url,$params);
        
        if ($response->successful()) {
            return $response->json();
        } 
        \Log::error($url . $response->body());
        return false;
    }
}

