<?php
namespace Microservices\models;

abstract class Model
{
    protected $access_token = '';
    protected $person_token = '';

    public function setToken($type = 'system') {
        $this->person_token = \Request::cookie('imap_authen_access_token');
        if ($type == 'system') {
            $this->access_token = env('API_MICROSERVICE_TOKEN','');
        }
        else {
            $this->access_token = $this->person_token;
        }
    }
    public function getCacheTag($tagsAdd = []) {
        if (!is_array($tagsAdd)) {
            $tagsAdd = [$tagsAdd];
        }
        foreach ($tagsAdd as $tag) {
            $tags[] = $this->service.':'.$this->table.':'.$tag;
        }
        return $tags;
    }
    public function getCacheKey($key) {
        if (is_array($key)) {
            $key = implode(':',$key);
        }
        return $this->service.':'.$this->table.':'.$key;
    }
    public function all($params = [], $options = [])
    {
        $filter = [];
        foreach($params as $k => $v){
            if (is_null($v)) continue;
            switch ($k) {
                default:
                    $filter[$k] = $v;
                    break;
            }
        }
        $q = $options;
        $q['filter'] = $filter;
        $response = \Http::acceptJson()->withToken($this->access_token)->get($this->_url.'/'.$this->prefix, $q);
        if ($response->successful()) {
            return $response->json();
        } 
        \Log::error($this->_url . $response->body());
        return false;
    }
    public function details($id, $options = []) {
        $arrData = [];
        $isCache = (!empty($this->is_cache) && \Cache::supportsTags()) ? 1 : 0;
        if ($isCache) {
            $tags = $this->getCacheTag();
            $arrData = \Cache::tags($tags)->many($id);
            $arrData = \Arr::whereNotNull($arrData);
            ////// lay cac key data ///
            if ($arrData) {
                if (!empty($options['select'])) {
                    $arrData = \Arr::map($arrData, function ($value, $key) use($options) {
                        return \Arr::only($value,$options['select']);
                    });
                }
                $arrKeysHit = array_keys($arrData);
                $id = array_diff($id,$arrKeysHit);
            }
        }
        if ($id) {
            $primaryKey = $this->primaryKey ?? '_id';
            $data = $this->all([$primaryKey => $id],$options);
            $data = \Arr::keyBy($data, $primaryKey);
            $arrData = $arrData + $data;
        }
        return $arrData;
    }
    public function detail($id, $options = [])
    {
        if (is_array($id)) {
            return $this->details($id,$options);
        }
        $isCache = (!empty($this->is_cache) && \Cache::supportsTags()) ? 1 : 0;
        if ($isCache) {
            $tags = $this->getCacheTag();
            $detail = \Cache::tags($tags)->get($id);       
            if ($detail) {
                if (!empty($options['select'])) {
                    $detail = \Arr::only($detail,$options['select']);
                }
                return $detail;
            }     
        }
        $url = $this->_url.'/'.$this->prefix.'/'.$id;
        $response = \Http::acceptJson()->withToken(env('API_MICROSERVICE_TOKEN',''))->get($url,$options);
        if ($response->successful()) {
            return $response->json();
        } 
        \Log::error($url . $response->body());
        return false;
    }

    public function create(array $params)
    {
        $params = \Arr::whereNotNull($params);
        if (!empty($this->only['create'])) {
            $params = \Arr::only($params, $this->only['create']);
        }
        if (!empty($this->dataDefault['create'])) {
            $params = array_merge($this->dataDefault['create'], $params);
        }
        if (!empty($this->idAutoIncrement) && empty($params[$this->primaryKey])) {
            $params[$this->primaryKey] = $this->getNextSequence($this->table);
        }
        $params['created_time'] = time();
        $url = $this->_url.'/'.$this->prefix;
        $response = \Http::acceptJson()->withToken($this->access_token)->POST($url, $params);
        if ($response->successful()) {
            return $response->json();
        } 
        \Log::error($url . $response->body());
        return false;
    }


    public function update($id, $params) 
    {
        // $params = \Arr::whereNotNull($params);

        if (!empty($this->only['update'])) {
            $params = \Arr::only($params, $this->only['update']);
        }
        if (!empty($this->idAutoIncrement)) {
            $id = (int) $id;
        }
        $url = $this->_url.'/'.$this->prefix.'/'.$id;
        $response = \Http::acceptJson()->withToken($this->access_token)->PUT($url, $params);
        if ($response->successful()) {
            return $response->json();
        } 
        \Log::error($url . $response->body());
        return false;
    }

    public function remove($id)
    {
        $url = $this->_url.'/'.$this->prefix.'/'.$id;
        $response = \Http::acceptJson()->withToken($this->access_token)->DELETE($url);
        if ($response->successful()) {
            return $response->json();
        } 
        \Log::error($this->$url . $response->body());
        return false;
    }
}
