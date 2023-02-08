<?php
namespace Microservices\models;

abstract class Model
{
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
        $q = ['filter' => $filter];
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/'.$this->prefix, $q);
        if ($response->successful()) {
            return $response->json();
        } 
        \Log::error($response->body());
        return false;
    }

    public function detail($id)
    {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/'.$this->prefix.'/'.$id);
        if ($response->successful()) {
            return $response->json();
        } 
        \Log::error($response->body());
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
        $params = $this->filter($params);
        if (!empty($this->idAutoIncrement) && empty($params[$this->primaryKey])) {
            $params[$this->primaryKey] = $this->getNextSequence($this->table);
        }
        $params['created_time'] = time();
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->POST($this->_url.'/'.$this->prefix, $params);
        if ($response->successful()) {
            return $response->json();
        } 
        \Log::error($response->body());
        return false;
    }


    public function update($id, $params) 
    {
        // $params = \Arr::whereNotNull($params);

        if (!empty($this->only['update'])) {
            $params = \Arr::only($params, $this->only['update']);
        }
        $params = $this->filter($params);
        if (!empty($this->idAutoIncrement)) {
            $id = (int) $id;
        }
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->PUT($this->_url.'/'.$this->prefix.'/'.$id, $params);
        if ($response->successful()) {
            return $response->json();
        } 
        \Log::error($response->body());
        return false;
    }

    public function remove($id)
    {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->DELETE($this->_url.'/'.$this->prefix.'/'.$id);
        if ($response->successful()) {
            return $response->json();
        } 
        \Log::error($response->body());
        return false;
    }

    public function filter($params)
    {
        if (empty($this->casts) || empty($params)) {
            return $params;
        }
        //$params = \Arr::dot($params);

        $result = [];
        $arrKeys = array_keys($params);
        foreach ($this->casts as $formatType => $v) {
            //////////// CHECK CAC KEY TRUNG //////
            //var_dump($v,$arrKeys);
            // $keysIntersect = array_intersect($v,$arrKeys);
            // if (!$keysIntersect) {
            //     continue;
            // }

            foreach ($v as $key) {

                if (isset($params[$key])) {
                    $isRegex = 0;
                } elseif (strpos($key, '.') !== false) {
                    $arr = explode('.', $key, 2);
                    if (!isset($params[$arr[0]])) {
                        continue;
                    }
                    $data = \Arr::dot($params[$arr[0]]);
                    $isRegex = 1;
                } else {

                    continue;
                }

                switch ($formatType) {
                    case 'integer':
                        if ($isRegex == 0) {
                            $params[$key] = (!is_array($params[$key])) ? (int) $params[$key] : array_map("intval", $params[$key]);
                        } else {

                            foreach ($data as $k => $dt) {
                                $kk = preg_replace('/(\.|^)(\d+)(\.|$)/', '.', $k);
                                $kk = trim($kk, '.');
                                //var_dump($arr[0].'.'.$k,$key);
                                if ($arr[0] . '.' . $kk == $key) {
                                    $data[$k] = (int) $dt;
                                }
                            }
                            $data = \Arr::undot($data);
                            $params[$arr[0]] = $data;
                        }

                        break;
                    case 'string':
                        $params[$key] = (!is_array($params[$key])) ? (string) $params[$key] : array_map("strval", $params[$key]);
                        break;
                    case 'unixtime':

                        if ((!is_array($params[$key]))) {
                            $params[$key] = (is_numeric($params[$key])) ? (int) $params[$key] : strtotime($params[$key]);
                        } else {
                            if (array_keys($params[$key]) !== range(0, count($params[$key]) - 1)) { // nhieu chieu
                                foreach ($params[$key] as $k => $v) {
                                    if (is_null($v)) {
                                        unset($params[$key][$k]);
                                        continue;
                                    }
                                    $params[$key][$k] = (is_numeric($v)) ? (int) $v : strtotime($v);
                                }
                            } else {
                                $params[$key] = array_map(function ($item) {
                                    return (is_numeric($item)) ? (int) $item : strtotime($item);
                                }, $params[$key]);
                            }

                        }
                        break;
                }
            }
        }
        return $params;
    }
}
