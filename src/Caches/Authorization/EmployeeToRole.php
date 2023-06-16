<?php

namespace Microservices\Caches\Authorization;

class EmployeeToRole extends \Microservices\Caches\BaseCache
{
    protected $_url;
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
    // public function rememberMe($userId,$params,$callback) {
    //     $tags = $this->getCacheTag('me:'.$userId);
    //     $keys = $params['service'].':'.$params['group'];
    //     return Cache::tags($tags)->remember($keys, 3600, $callback);
    // }
    public function putMe($userId,$params,$values) {
        $tags = $this->getCacheTag('me:'.$userId);
        $keys = $params['service'].':'.$params['group'];     
        return \Cache::tags($tags)->put($keys,$values,3600);  
    }
    public function flushMe($userId) {
        $tags = $this->getCacheTag('me:'.$userId);
        return \Cache::tags($tags)->flush();  
    }
}

