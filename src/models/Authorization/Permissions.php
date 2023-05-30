<?php

namespace Microservices\models\Authorization;

class Permissions extends \Microservices\models\Model
{
    protected $_url;
    protected $prefix = 'permission';
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
        // $tags = [$params['service'],$params['group']];

        // $arrData = \Cache::tags($tags)->many($id);
        if (config('app.service_code') == 'erp_authorization_backend_v2') {
            
            return (new \App\Models\Permission)->permissionByEmployee($userId,$params);
        }
        else {
            $url = $this->_url.'/'.$this->prefix.'/me';
            $response = \Http::withToken($this->person_token)->get($url,$params);
            if ($response->successful()) {
                return $response->json();
            } 
        }
        
        \Log::error($url . $response->body());
        return false;
    }
}

