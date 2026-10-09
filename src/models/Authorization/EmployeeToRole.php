<?php

namespace Microservices\models\Authorization;

class EmployeeToRole extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/authorization/employee';
        
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
        $result = $this->safeGet($url, $params, (string) $this->getToken());
        return $result === false ? false : $result;
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
        $result = $this->safeGet($url, $params, (string) $this->getToken());
        return $result === false ? false : $result;
    }
    public function getAllPermission($params) {
        ////// GET FROM CACHE ////////
        $permission = $this->cache()->getAllPermission($params);
        if ($permission) {
            return $permission;
        }
        ////// MISS CACHE //////////
        $url = $this->_url.'/permission';
        $result = $this->safeGet($url, $params, (string) $this->getToken());
        return $result === false ? false : $result;
    }
}
