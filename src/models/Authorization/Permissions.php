<?php

namespace Microservices\models\Authorization;

class Permissions extends \Microservices\models\Model
{
    protected $_url;
    protected $prefix = 'permission';
    protected $service = 'erp_authorization_backend_v2';
    protected $table = 'permission';
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/authorization';
        $this->setToken($options['token'] ?? 'system');
    }
    public function me($params)
    {
        $userId = \Auth::id();
        if (!$userId) {
            return false;
        }
        ////// GET FROM CACHE ////////
        $key = $this->getCacheKey([$userId, $params['service']]);
        $permissions = \Cache::get($key);
        if (!empty($permissions[$params['group']])) {
            return $permissions[$params['group']];
        }
        ////// MISS CACHE //////////
        $url = $this->_url.'/'.$this->prefix.'/me';
        $response = \Http::withToken($this->person_token)->get($url,$params);
        
        if ($response->successful()) {
            return $response->json();
        } 
        \Log::error($url . $response->body());
        return false;
    }
}

