<?php
namespace Microservices\Caches;

class BaseCache 
{
    protected $cacheDetailTime = 86400;

    public function getCacheTag($tagsAdd) {
        
        if (!is_array($tagsAdd)) {
            $tagsAdd = [$tagsAdd];
        }
        $prefix = $this->service .':'.$this->table;
        foreach ($tagsAdd as $tag) {
            $tags[] = $prefix.':'.$tag;
        }
        return $tags;
    }
    public function getCacheKey($key) {
        $prefix = $this->service.':'.$this->table.':';
        if (is_array($key)){
            return array_map(function($item) use($prefix) {
                return $prefix.$item;
            },$key);
        }
        else {
            $key = $prefix.$key;
        }
    }
    public function getCacheExpire($type = 'detail') {
        switch ($type) {
            default:
            $time = $this->cacheDetailTime;
        }
        return $time;
    }
}