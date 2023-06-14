<?php
namespace Microservices\models;

use Illuminate\Support\Facades\DB;
use MongoDB\Operation\FindOneAndUpdate;

abstract class BaseModel
{
    protected $cacheDetailTime = 86400;
    
    public function count($params)
    {
        $params = $this->filter($params);
        $query = \DB::table($this->table);
        $this->setWhere($query, $params);
        return $query->count();
    }
    public function all($params = [], $options = [])
    {
        if (!empty($this->only['lists'])) {
            $arrOnly = array_merge($this->only['lists'], [$this->primaryKey]);
            $params = \Arr::only($params, $arrOnly);
        }
        $params = $this->filter($params);
        $query = \DB::table($this->table);
        if (!empty($options['select'])) {
            if (!is_array($options['select'])) {
                $options['select'] = explode(',',$options['select']);
            }
            $query->select($options['select']);
        }
        if ($params) {
            $this->setWhere($query, $params);
        }
        if (empty($options['order_by'])) {
            $options['order_by'] = [$this->primaryKey, 'DESC'];
        }
        $query->orderBy($options['order_by'][0], $options['order_by'][1] ?? "ASC");
        if (!empty($options['pagination'])) {
            return $query->simplePaginate($options['limit'] ?? config('data.default_limit_pagination'));
        } else {
            return $query->limit($options['limit'] ?? 100)->offset($options['offset'] ?? 0)->get();
        }
        
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
        if (!empty($params[$this->primaryKey] && $params[$this->primaryKey] != "_id")) {
            $params['_id'] = $this->getNextSequence($this->table);
        }
        if (empty($params['created_by']) && \Auth::id()) {
            $params['created_by'] = (int) \Auth::id();
        }
        $params['created_time'] = time();
        $params['updated_time'] = time();

        return DB::table($this->table)->insertGetId($params);
    }
    public function createBatch(array $multiParams)
    {
        foreach ($multiParams as $k => $params) {
            if (!empty($this->only['create'])) {
                $params = \Arr::only($params, $this->only['create']);
            }
            if (!empty($this->dataDefault['create'])) {
                $params = array_merge($this->dataDefault['create'], $params);
            }
            $params = $this->filter($params);
            if ($this->idAutoIncrement) {
                $params[$this->primaryKey] = $this->getNextSequence($this->table);
            }
            $params['created_by'] = (int) \Auth::id();
            $params['created_time'] = time();
            $params['updated_time'] = time();
            $multiParams[$k] = $params;
        }
        return \DB::table($this->table)->insert($multiParams);
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
        $params['updated_time'] = time();
        $result = \DB::table($this->table)->where($this->primaryKey, $id)->update($params);
        if (!empty($this->is_cache) && \Cache::supportsTags()) {
            $tags = $this->getCacheTag();
            \Cache::tags($tags)->forget($id);
        }
        return $result;
        //$query->update($params);
    }
    public function updateBatch($conditions, $params)
    {
        if (!empty($this->only['updateBatchCondition'])) {
            $conditions = \Arr::only($conditions, $this->only['updateBatchCondition']);
        }
        if (!empty($this->only['updateBatch'])) {
            $params = \Arr::only($params, $this->only['updateBatch']);
        }
        $params = $this->filter($params);
        $condition = $this->filter($conditions);
        $params['updated_time'] = time();
        /// update
        $query = \DB::table($this->table);
        $this->setWhere($query, $conditions);
        $query->update($params);
        /// clear cache
        if (!empty($this->is_cache) && \Cache::supportsTags()) {
            $tags = $this->getCacheTag();
            \Cache::tags($tags[1])->flush();
        }
    }
    public function deleteBatch($conditions)
    {
        if (!empty($this->only['deleteBatch'])) {
            $conditions = \Arr::only($conditions, $this->only['deleteBatch']);
        }
        $conditions = $this->filter($conditions);
        if(empty($conditions)){
            return false;
        }
        $query = \DB::table($this->table);
        $this->setWhere($query, $conditions);
        $query->delete();
        // xoa cache
        if (!empty($this->is_cache) && \Cache::supportsTags()) {
            $tags = $this->getCacheTag();
            \Cache::tags($tags[1])->flush();
        }
    }
    public function details($id, $options = []) {
        if (!empty($this->idAutoIncrement)) {
            $id = array_map('intval',$id);
        }
        $arrData = [];
        $isCache = (!empty($this->is_cache) && \Cache::supportsTags()) ? 1 : 0;
        $queryOptions = ['limit' => 1000];
        if ($isCache) {
            $tags = $this->getCacheTag();
            if (empty($options['reset_cache'])) {
                $arrData = \Cache::tags($tags)->many($id);
                $arrData = \Arr::whereNotNull($arrData);
                ////// lay cac key data ///
                if ($arrData) {
                    $arrKeysHit = array_keys($arrData);
                    $id = array_diff($id,$arrKeysHit);
                }
            }
        }
        else {
             //////// NEU KO SU DUNG CACHE SE CHI QUERY DU //////
            $queryOptions = array_merge($queryOptions,$options);
        }
        if ($id) {
            /// QUERY DATA
            $data = $this->all([$this->primaryKey => $id],$queryOptions)->keyBy($this->primaryKey)->all();
            $arrData = $arrData + $data;
            ///////
        }
        if ($isCache && !empty($data)) {
            \Cache::tags($tags)->putMany($data,$this->cacheDetailTime);
            unset($data);
            if (!empty($options['select'])) {
                $arrData = \Arr::map($arrData, function ($value, $key) use($options) {
                    return \Arr::only($value,$options['select']);
                });
            }
        }
        return $arrData;
    }
    public function detail($id,$options = [])
    {
        if (is_array($id)) {
            $this->details($id, $options);
        }
        if (!empty($this->idAutoIncrement)) {
            $id = (int) $id;
        }
        //////// CHECK CACHE ////////
        $data = $queryOptions = [];
        $isCache = (!empty($this->is_cache) && \Cache::supportsTags()) ? 1 : 0;
        //////// GET CACHE ////////
        if ($isCache) {
            $tags = $this->getCacheTag();
            if (empty($options['reset_cache'])) {
                $data = \Cache::tags($tags)->get($id);
            }
        }
        else {
            $queryOptions = array_merge($queryOptions, $options);
        }

        //////// NEU KO SU DUNG CACHE SE CHI QUERY DU //////
        if (!$data) {
            $query = \DB::table($this->table)->where($this->primaryKey, $id);
            if (!empty($queryOptions['select'])) {
                $query->select([$options['select']]);
            }
            $data = $query->first();
        }
        //////// SET CACHE /////
        if ($isCache && $data) {
            \Cache::tags($tags)->put($id,$data,$this->cacheDetailTime);
            if (!empty($options['select'])) {
                $data = \Arr::only($data,$options['select']);
            }
        }
        return $data;
    }
    public function remove($id)
    {
        if (!empty($this->idAutoIncrement)) {
            $id = (int) $id;
        }
        if (!empty($this->is_cache) && \Cache::supportsTags()) {
            $tags = $this->getCacheTag();
            \Cache::tags($tags)->forget($id);
        }
        return \DB::table($this->table)->where($this->primaryKey, $id)->delete();
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
                    if (is_array($params[$key])) {
                        $params[$key] = \Arr::whereNotNull($params[$key]);
                        if (empty($params[$key])) {
                            unset($params[$key]);
                            continue;
                        }
                    }
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
    public function aggregate($aggregate)
    {
        $aggregate = $this->replaceKey($aggregate);
        if (!empty($aggregate[0]['$match'])) {
            $aggregate[0]['$match'] = $this->getMatch($aggregate[0]['$match']);
        }
        return \DB::collection($this->table)->raw(function ($collection) use ($aggregate) {
            return $collection->aggregate(
                $aggregate
            );
        });
    }
    public function getNextSequence($name)
    {
        $seq = \DB::getCollection('counters')->findOneAndUpdate(
            array('_id' => $name),
            array('$inc' => array('seq' => 1)),
            array('new' => true, 'upsert' => true, 'returnDocument' => FindOneAndUpdate::RETURN_DOCUMENT_AFTER)
        );
        return $seq->seq;
    }
    public function replaceKey($arr)
    {
        if (empty($arr)) {
            return $arr;
        }
        $dataEncoded = json_encode($arr);
        $dataEncoded = str_replace('"gt":', '"$gt":', $dataEncoded);
        $dataEncoded = str_replace('"lt":', '"$lt":', $dataEncoded);
        $dataEncoded = str_replace('"lte":', '"$lte":', $dataEncoded);
        $dataEncoded = str_replace('"gte":', '"$gte":', $dataEncoded);
        return json_decode($dataEncoded, true);
    }
    public function getMatch($params)
    {
        $params = $this->filter($params);

        foreach ($params as $k => $v) {
            if (is_array($v)) {
                // kiem tra mang nhieu chieu hay 1 chieu
                if (array_keys($v) === range(0, count($v) - 1)) { // nhieu chieu
                    $params[$k] = ['$in' => $v];
                }
            } else {

            }
        }
        return $params;
    }
    private function setWhere($query, $conditions)
    {
        foreach ($conditions as $k => $v) {

            if (is_array($v)) {
                // kiem tra mang nhieu chieu hay 1 chieu
                if (array_keys($v) !== range(0, count($v) - 1)) { // nhieu chieu
                    foreach ($v as $condition => $v) {
                        if (is_null($v) || $v == '') {
                            continue;
                        }
                        switch ($condition) {
                            case 'gt':
                                //die("OK2");
                                $query->where($k, '>', $v);
                                break;
                            case 'gte':
                                $query->where($k, '>=', $v);
                                break;
                            case 'lt':
                                $query->where($k, '<', $v);
                                break;
                            case 'lte':
                                $query->where($k, '<=', $v);
                                break;
                            case 'ne':
                                $query->where($k, '<>', $v);
                                break;
                            case 'like':
                                $query->where($k, 'like', "%" . $v . "%");
                                break;
                            case 'elemmatch':
                                foreach ($v as $kk => $vv) {
                                    if (is_array($vv)) {
                                        if (!empty($vv[0])) {
                                            $v[$kk] = ['$in' => $vv];
                                        } else {
                                            foreach ($vv as $kkk => $vv) {
                                                $v[$kk] = ['$' . $kkk => $vv];
                                            }
                                        }

                                    }
                                }
                                $query->where($k, 'elemmatch', $v);
                                break;
                            default:

                        }
                    }
                } else {
                    $query->whereIn($k, $v);
                }
            } else {
                if (is_null($v) || $v == '') {
                    continue;
                }
                $query->where($k, $v);
            }

        }
    }
    public function getCacheTag($tagsAdd) {
        if (!is_array($tagsAdd)) {
            $tagsAdd = [$tagsAdd];
        }
        $prefix = config('app.service_code').':'.$this->table;
        foreach ($tagsAdd as $tag) {
            $tags[] = $prefix.':'.$tag;
        }
        return $tags;
    }
    public function getCacheKey($key) {
        $service = config('app.service_code');
        if (is_array($key)) {
            $key = implode(':',$key);
        }
        return $service.':'.$this->table.':'.$key;
    }
    public function getCacheExpire($type = 'detail') {
        switch ($type) {
            default:
            $time = $this->cacheDetailTime;
        }
        return $time;
    }
}