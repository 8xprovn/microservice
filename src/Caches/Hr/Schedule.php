<?php


namespace Microservices\Caches\Hr;


class Schedule extends \Microservices\Caches\BaseCache
{
    protected $service = 'hr';
    protected $table = 'employee_schedules';
    public function __construct($options = []) {
    }

    public function getMe($userId, $key_cache)
    {

        if (!\Cache::supportsTags()) {
            return null;
        }
        $tags = $this->getCacheTag('schedule_me:'.$userId);
        $keys = $this->service.':'.$key_cache;
        return \Cache::tags($tags)->get($keys);
    }
    public function putMe($userId,$key_cache,$values, $time = 3600) {
        if (!\Cache::supportsTags()) {
            return null;
        }
        $tags = $this->getCacheTag('schedule_me:'.$userId);
        $keys = $this->service.':'.$key_cache;
        return \Cache::tags($tags)->put($keys,$values,$time);
    }
    public function flushMe($userId) {
        if (!\Cache::supportsTags()) {
            return null;
        }
        $tags = $this->getCacheTag('schedule_me:'.$userId);
        return \Cache::tags($tags)->flush();
    }
}
