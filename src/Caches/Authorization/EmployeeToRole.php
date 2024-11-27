<?php

namespace Microservices\Caches\Authorization;

class EmployeeToRole extends \Microservices\Caches\BaseCache
{
    protected $service = 'erp_authorization_backend_v2';
    protected $table = 'authorization_employee_to_roles';
    public function __construct($options = []) {
    }
    public function getMe($userId, $params)
    {
        if (!\Cache::supportsTags()) {
            return null;
        }
        $tags = $this->getCacheTag('me:'.$userId);
        $keys = $params['service'].':'.$params['group'];
        return \Cache::tags($tags)->get($keys);
    }
    public function putMe($userId,$params,$values) {
        if (!\Cache::supportsTags()) {
            return null;
        }
        $tags = $this->getCacheTag('me:'.$userId);
        $keys = $params['service'].':'.$params['group'];     
        return \Cache::tags($tags)->put($keys,$values,3600);  
    }
    public function flushMe($userId) {
        if (!\Cache::supportsTags()) {
            return null;
        }
        $tags = $this->getCacheTag('me:'.$userId);
        return \Cache::tags($tags)->flush();  
    }
    /**
     * GET Cache permission cua 1 user
     * @param mixed $params service, user_id
     * @return mixed
     */
    public function getAllPermission($params) {
        
        return $this->detail($params['service'].':'.$params['user_id']);
    }
    /**
     * SET Cache permission cua 1 user
     * @param mixed $params service, user_id
     * @return mixed
     */
    public function putAllPermission($params,$values) {
        return $this->update($params['service'].':'.$params['user_id'],$values);
    }
}

