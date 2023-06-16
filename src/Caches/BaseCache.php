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
    public function detail($id,$options = []) {
        if (!\Cache::supportsTags()) {
            return null;
        }
        $tags = $this->getCacheTag('detail');
        $data = \Cache::tags($tags)->get($id);
        if ($data && !empty($options['select'])) {
            if (is_array($id)) {
                $data = \Arr::map($data, function ($value, $key) use($options) {
                    return \Arr::only($value,$options['select']);
                });
            } else {
                $data = \Arr::only($data,$options['select']);
            }
        }
        return $data;
    }
    public function delete($id,$tag = 'detail') {
        if (!\Cache::supportsTags()) {
            return null;
        }
        $tags = $this->getCacheTag($tag);
        \Cache::tags($tags)->forget($id);
    }
    public function flush($tag = 'detail') {
        if (!\Cache::supportsTags()) {
            return null;
        }
        $tags = $this->getCacheTag($tag);
        \Cache::tags($tags)->flush();
    }
    public function update($key,$data = [],$tag = 'detail') {
        if (!\Cache::supportsTags()) {
            return null;
        }
        $tags = $this->getCacheTag($tag);
        if (is_array($key)) {
            \Cache::tags($tags)->putMany($key,$this->cacheDetailTime);
        }
        else {
            \Cache::tags($tags)->put($key,$data, $this->cacheDetailTime);
        }
        
    }
}