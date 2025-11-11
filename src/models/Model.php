<?php
namespace Microservices\models;

use Microservices\Traits\SafeHttpClient;
use Illuminate\Support\Facades\Request;

abstract class Model
{
    use SafeHttpClient;
    protected $access_token = '';
    protected $access_token_type = '';
    protected $person_token = '';

    public function setToken($type = 'system') {
        switch($type) {
            case 'system':
            $this->access_token = env('API_MICROSERVICE_TOKEN','');
            break;
            case 'cookie':
            $this->access_token = Request::cookie('imap_authen_access_token');
            break;
            case 'bearer':
            $this->access_token = Request::bearerToken();
            break;
            default:
            $this->access_token = $type;
            $type = 'custom';
        }
        $this->access_token_type = $type;
        return $this;
    }
    public function getToken() {
        
        if (empty($this->access_token)) {
            $this->setToken();
        }
        $token = $this->access_token;
        if ($this->access_token_type != 'system') {
            $this->setToken();
        }
        return $token;
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
        $accessToken = $this->getToken();
        // $response = \Http::acceptJson()
        //             ->withToken($accessToken)
        //             ->get($this->_url, $q);
        // if ($response->successful()) {
        //     return $response->json();
        // } 
        return $this->safeGet($this->_url, $q ,$accessToken);
    }
    public function details($id, $options = []) {
        $arrData = [];
        $primaryKey = $this->primaryKey ?? '_id';
        if (!empty($options['select']) && !in_array($primaryKey,  $options['select'])) {
            $options['select'][] = $primaryKey;
        }
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
                \Cache::put( $uuid , $id, 120);
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
        
        if (!empty($this->idAutoIncrement)) {
            $id = (int) $id;
        }
        $isCache = (!empty($this->is_cache) && empty($options['reset_cache'])) ? 1 : 0;
        if ($isCache) {
            $data = $this->cache()->detail($id,$options) ?? [];
            if ($data) {
                return $data;
            }
        }
        $url = $this->_url.'/'.$id;
        $accessToken = $this->getToken();
        return $this->safeGet($url, $options, $accessToken);
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
        $params['created_time'] = time();
        
        $accessToken = $this->getToken();
        return $this->safePost($this->_url, $params, $accessToken);
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
        $accessToken = $this->getToken();

        return $this->safePut($url, $params, $accessToken);
    }

    public function remove($id, $params = [])
    {
        $url = $this->_url.'/'.$id;
        $accessToken = $this->getToken();



        return $this->safeDelete($url,  $params, $accessToken );
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
