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
        return $key;
    }
    public function getCacheExpire($type = 'detail') {
        switch ($type) {
            default:
            $time = $this->cacheDetailTime;
        }
        return $time;
    }
    public function detail($id,$options = [],$tag = 'detail') {
        // if (!\Cache::supportsTags()) {
        //     return null;
        // }
        // $tags = $this->getCacheTag($tag);
        
        $key = $this->getCacheKey($id);
        $data = \Cache::get($key);
        $data = \Arr::whereNotNull($data);
        if (!$data) {
            return null;
        }
        // $id = $this->getCacheKey($id);
        // $data = \Cache::get($id);
        if (!empty($options['select'])) {
            if (is_array($id)) {
                $data = \Arr::map($data, function ($value, $key) use($options) {
                    return \Arr::only($value,$options['select']);
                });
            } else {
                $data = \Arr::only($data,$options['select']);
            }
        } 
        
        return array_values($data);
    }
    public function delete($ids,$tag = 'detail') {
        // if (!\Cache::supportsTags()) {
        //     return null;
        // }
        // $tags = $this->getCacheTag($tag);
        // \Cache::tags($tags)->forget($id);
        $ids = $this->getCacheKey($ids);
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        \Cache::connection()->del($ids);
        //\Cache::forget($ids);
        
    }
    public function flush($tag = 'detail') {
        // if (!\Cache::supportsTags()) {
        //     return null;
        // }
        // $tags = $this->getCacheTag($tag);
        // \Cache::tags($tags)->flush();
    }
    public function update($key,$data = [],$tag = 'detail') {
        // $tags = $this->getCacheTag($tag);
        // if (is_array($key)) {
        //     \Cache::tags($tags)->putMany($key,$this->cacheDetailTime);
        // }
        // else {
        //     \Cache::tags($tags)->put($key,$data, $this->cacheDetailTime);
        // }
        if (is_array($key)) {
            $prefix = $this->service.':'.$this->table.':';
            $key = \Arr::prependKeysWith($key, $prefix);
            $mapped = array_map('serialize',$key);
            \Cache::connection()->mset($mapped);
            //\Cache::putMany($key,$this->cacheDetailTime);
        }
        else {
            $key = $this->getCacheKey($key);
            \Cache::put($key,$data, $this->cacheDetailTime);
        }        
    }
}