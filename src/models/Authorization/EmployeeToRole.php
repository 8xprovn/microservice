<?php

namespace Microservices\models\Authorization;

class EmployeeToRole extends \Microservices\models\Model
{
    protected $_url;
    protected $service = 'erp_authorization_backend_v2';
    protected $table = 'authorization_employee_to_roles';
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
        if (\Cache::supportsTags()) {
            $tags = $this->getCacheTag('me:'.$userId);
            $keys = $params['service'].':'.$params['group'];
            $permissions = \Cache::tags($tags)->get($keys);
            if (!empty($permissions)) {
                return $permissions;
            }
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

