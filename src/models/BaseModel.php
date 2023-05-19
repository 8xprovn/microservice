<?php
namespace Microservices\models;

use Illuminate\Support\Facades\DB;
use MongoDB\Operation\FindOneAndUpdate;

abstract class BaseModel
{
    private $cacheDetailTime = 86400;
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
            $query->select([$options['select']]);
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
        if (!empty($this->is_cache)) {
            $tags = [config('app.service_code'),$this->table];
            \Cache::tags($tags)->forget($id);
        }
        return \DB::table($this->table)->where($this->primaryKey, $id)->update($params);
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
        /// get id to reset cache
        if (!empty($this->is_cache)) {
            $arrResetCache = $this->all($condition,['select' => $this->primaryKey,'limit' => 1000])->keyBy($this->primaryKey)->all();
        }
        // 
        // array_map()
        /// update
        $query = \DB::table($this->table);
        $this->setWhere($query, $conditions);
        $query->update($params);
        if (!empty($this->is_cache)) {
            $tags = [config('app.service_code'),$this->table];
            \Cache::tags($tags)->putMany($arrResetCache,-1);
        }
        /// reset cache ///

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
        if (!empty($this->is_cache)) {
            $arrResetCache = $this->all($condition,['select' => $this->primaryKey,'limit' => 1000])->keyBy($this->primaryKey)->all();
        }
        $query = \DB::table($this->table);
        $this->setWhere($query, $conditions);
        $query->delete();
        if (!empty($this->is_cache)) {
            $tags = [config('app.service_code'),$this->table];
            \Cache::tags($tags)->putMany($arrResetCache,-1);
        }
    }
    public function detail($id,$options = [])
    {
        $idDetail = 0;
        if (!is_array($id)) {
            $idDetail = $id;
            $id = [$id];
        }
        if (!empty($this->idAutoIncrement)) {
            $id = array_map('intval',$id);
        }
        //////// CHECK CACHE ////////
        $arrData = [];
        //////// GET CACHE ////////
        if (!empty($this->is_cache)) {
            $tags = [config('app.service_code'),$this->table];
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
        if ($id) {
            $data = $this->all([$this->primaryKey => $id],['limit' => 1000])->toArray();
            $data = \Arr::keyBy($data, $this->primaryKey);
            
            //////// SET CACHE /////
            if (!empty($this->is_cache) && $data) {
                \Cache::tags($tags)->putMany($data,$this->cacheDetailTime);
            }
            
            $arrData = $arrData + $data;
            ///////
            unset($data);
        }
        if (!empty($options['select'])) {
            if (!is_array($options['select'])) {
                $options['select'] = explode(',',$options['select']);
            }
            $arrData = \Arr::map($arrData, function ($value, $key) use($options) {
                return \Arr::only($value,$options['select']);
            });
        }
        if ($idDetail) {
            $arrData = $arrData[$idDetail] ?? [];
        }
        return $arrData;
    }
    public function remove($id)
    {
        if (!empty($this->idAutoIncrement)) {
            $id = (int) $id;
        }
        if (!empty($this->is_cache)) {
            $tags = [config('app.service_code'),$this->table];
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
}