<?php
namespace Microservices\models;

abstract class Model
{
    protected $access_token = '';
    protected $person_token = '';

    public function setToken($type = 'system') {
        switch($type) {
            case 'system':
            $this->access_token = env('API_MICROSERVICE_TOKEN','');
            break;
            case 'cookie':
            $this->access_token = \Request::cookie('imap_authen_access_token');
            break;
            case 'bearer':
            $this->access_token = \Request::bearerToken();
            break;
            default:
            abort(403, 'Unauthorized token type');
        }
    }
    public function getToken() {
        return $this->access_token;
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
        $response = \Http::acceptJson()
                    ->withToken($this->access_token)
                    ->get($this->_url, $q);
        if ($response->successful()) {
            return $response->json();
        } 
        \Log::error($this->_url . $response->body());
        return false;
    }
    public function details($id, $options = []) {
        $arrData = [];
        $primaryKey = $this->primaryKey ?? '_id';
        $isCache = (!empty($this->is_cache) && empty($options['reset_cache'])) ? 1 : 0;
        if ($isCache) {
            $arrData = $this->cache()->detail($id,$options) ?? [];
            ////// lay cac key data ///
            if ($arrData) {
                $arrKeysHit = \Arr::pluck($arrData,$primaryKey);
                $id = array_diff($id,$arrKeysHit);
            }
            ////// lay cac key data ///
        }
        if ($id) {
            /////// NEU PHAN TU > 100 SE SU DUNG UUID //////
            if (count($id) > 100) {
                $uuid = (string) \Str::uuid();
                $cacheResult = \Cache::put( $uuid , $id, 120);
                $id = $uuid;
            }
            $data = $this->all([$primaryKey => $id],$options);
            if ($data) {
                $arrData = $arrData + $data;
            }
        }
        return $arrData;
    }
    public function detail($id, $options = [])
    {
        if (is_array($id)) {
            return $this->details($id,$options);
        }
        $isCache = (!empty($this->is_cache) && empty($options['reset_cache'])) ? 1 : 0;
        if ($isCache) {
            $data = $this->cache()->detail($id,$options) ?? [];
            if ($data) {
                return $data;
            }
        }
        $url = $this->_url.'/'.$id;
        $response = \Http::acceptJson()
                    ->withToken(env('API_MICROSERVICE_TOKEN',''))
                    ->get($url,$options);
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
        $url = $this->_url;
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
        $url = $this->_url.'/'.$id;
        $response = \Http::acceptJson()->withToken($this->access_token)->PUT($url, $params);
        if ($response->successful()) {
            return $response->json();
        } 
        \Log::error($url . $response->body());
        return false;
    }

    public function remove($id, $options = [])
    {
        $url = $this->_url.'/'.$id;
        $response = \Http::acceptJson()->withToken($this->access_token)->DELETE($url, $options);
        if ($response->successful()) {
            return $response->json();
        } 
        \Log::error($this->$url . $response->body());
        return false;
    }
    /**
     * @author: namtq
     * @todo: load class cache same name
     */
    public function cache() {
        $className = get_called_class();
        $className = str_replace('Microservices\models\\','',$className);
        return \Microservices::loadCache($className);
    }
}
