<?php

namespace Microservices\models;

use Illuminate\Support\Facades\DB;
use MongoDB\Operation\FindOneAndUpdate;
use function MongoDB\is_first_key_operator;

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
    public function sum($params, $field)
    {
        $params = $this->filter($params);
        $query = \DB::table($this->table);
        $this->setWhere($query, $params);
        return $query->sum($field);
    }
    public function all($params = [], $options = [])
    {
        if (!empty($this->only['lists'])) {
            $arrOnly = array_merge($this->only['lists'], [$this->primaryKey]);
            $params = \Arr::only($params, $arrOnly);
        }
        if (!empty($this->isSoftDelete)) {
            $params = array_merge($params,['is_deleted' => 0]);
        }
        ////// KTRA NEU CHI LAY THEO ID 
        // dd($params);
        if (count($params) == 1 && !empty($params[$this->primaryKey])) {
            $data = $this->details($params[$this->primaryKey], $options);
            return collect($data);
        }
        if (!empty($this->dataDefault['lists'])) {
            $params = array_merge($this->dataDefault['lists'],$params);
        }
        
        $params = $this->filter($params);
        $query = \DB::table($this->table);
        if (!empty($options['select'])) {
            if (!is_array($options['select'])) {
                $options['select'] = explode(',', $options['select']);
            }
            $query->select($options['select']);
        }
        if ($params) {
            $this->setWhere($query, $params);
        }

        if (empty($options['order_by'])) {
            $options['order_by'] = [$this->primaryKey => 'DESC'];
        }
        if (!is_array($options['order_by'])) {
            $options['order_by'] = [$options['order_by'] => 'ASC'];
        }
        if (!empty($options['order_by'][0])) {
            $options['order_by'][1] = $options['order_by'][1] ?? 'ASC';
            $options['order_by'] = [$options['order_by'][0] => $options['order_by'][1]];
        }
        foreach ($options['order_by'] as $k => $v) {
            $query->orderBy($k, $v);
        }
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
            if (empty($params['created_by']) && \Auth::id()) {
                $params['created_by'] = (int) \Auth::id();
            }
            $params['created_time'] = time();
            $params['updated_time'] = time();
            $multiParams[$k] = $params;
        }
        return \DB::table($this->table)->insert($multiParams);
    }
    public function update($id, $params, $options = [])
    {
        // $params = \Arr::whereNotNull($params);

        if (!empty($this->only['update'])) {
            if (is_first_key_operator($params)) {
                foreach ($params as $k => $v) {
                    $v = \Arr::only($v, $this->only['update']);
                    if (empty($v)) {
                        unset($params[$k]);
                    } else {
                        $params[$k] = $v;
                    }
                }
            } else {
                $params = \Arr::only($params, $this->only['update']);
            }
        }
        if (empty($params)) {
            return false;
        }
        $params = $this->filter($params);
        if (!empty($this->idAutoIncrement)) {
            $id = (int) $id;
        }
        if (is_first_key_operator($params)) {
            $params['$set'] = array_merge($params['$set'] ?? [], ['updated_time' => time()]);
        } else {
            $params['updated_time'] = time();
        }

        ////////// CHECK OPTION RETURN //////
        if (!empty($options['isReturnData'])) {
            $result = \DB::getCollection($this->table)->findOneAndUpdate(
                [$this->primaryKey => $id],
                $params,
                array('new' => false, 'upsert' => $options['upsert'] ?? false, 'returnDocument' => FindOneAndUpdate::RETURN_DOCUMENT_AFTER),
            );
            if ($result) {
                $result = iterator_to_array($result);
            }
        } else {
            $result = \DB::table($this->table)->where($this->primaryKey, $id)->update($params, $options);
        }
        if (!empty($this->is_cache)) {
            $this->cache()->delete($id);
        }
        return $result;
        //$query->update($params);
    }
    public function updateBatch($conditions, $params, $options = [])
    {
        if (!empty($this->only['updateBatchCondition'])) {
            $conditions = \Arr::only($conditions, $this->only['updateBatchCondition']);
        }
        if (!empty($this->only['updateBatch'])) {
            $params = \Arr::only($params, $this->only['updateBatch']);
        }
        $params = $this->filter($params);
        $conditions = $this->filter($conditions);
        if (empty($conditions) || empty($params)) {
            return false;
        }
        if (is_first_key_operator($params)) {
            $params['$set'] = array_merge($params['$set'] ?? [], ['updated_time' => time()]);
        } else {
            $params['updated_time'] = time();
        }
        ////////// UPDATE DATA ////////


        //
        $arrIds = [];
        if (!empty($options['isReturnData'])) {
            $result = \DB::getCollection($this->table)->findOneAndUpdate(
                $conditions,
                $params,
                array('new' => false, 'upsert' => $options['upsert'] ?? false, 'returnDocument' => FindOneAndUpdate::RETURN_DOCUMENT_AFTER),
            );
            if ($result) {
                $result = iterator_to_array($result);
                if (!empty($result['_id'])) {
                    $arrIds = [$result['_id']];
                }
            }
        } else {
            $query = \DB::table($this->table);
            $this->setWhere($query, $conditions);
            if (!empty($this->is_cache)) {
                $query->select($this->primaryKey);
                $arrIds = $query->get()->pluck($this->primaryKey)->all();
            }
            $result = $query->update($params, $options);
        }


        ////////// FLUSH CACHE ///////////
        if (!empty($this->is_cache) && $arrIds) {
            $this->cache()->delete($arrIds);
        }
        return $result;
        /// clear cache
    }
    public function deleteBatch($conditions)
    {
        if (!empty($this->only['deleteBatch'])) {
            $conditions = \Arr::only($conditions, $this->only['deleteBatch']);
        }
        $conditions = $this->filter($conditions);
        if (empty($conditions)) {
            return false;
        }

        ////////// UPDATE DATA ////////
        $query = \DB::table($this->table);
        $this->setWhere($query, $conditions);
        if (!empty($this->is_cache)) {
            $query->select($this->primaryKey);
            $arrIds = $query->get()->pluck($this->primaryKey)->all();
        }
        $result = $query->delete();
        ////////// FLUSH CACHE ///////////
        if (!empty($this->is_cache) && $arrIds) {
            $this->cache()->delete($arrIds);
        }
        return $result;
    }
    public function details($id, $options = [])
    {
        if (!is_array($id)) {
            ////// KTRA UUID KO //////
            if (\Str::isUuid($id)) {
                $id = \Cache::get($id);
                if (!$id) {
                    \Log::error('Microservice: Không tìm thấy cache id');
                    return [];
                }
            } else {
                $id = [$id];
            }
        }
        if (!empty($this->idAutoIncrement)) {
            $id = array_map('intval', $id);
        }
        $arrData = [];
        $isCache = (!empty($this->is_cache) && empty($options['reset_cache'])) ? 1 : 0;
        if (!empty($options['select'])) {
            $options['select'] = is_array($options['select']) ? $options['select'] : explode(',', $options['select']);
            array_push($options['select'], $this->primaryKey);
        }
        if ($isCache) {
            $arrData = $this->cache()->detail($id, $options) ?? [];
            ////// lay cac key data ///
            if ($arrData) {
                $arrKeysHit = \Arr::pluck($arrData, $this->primaryKey);
                $id = array_diff($id, $arrKeysHit);
            }
        }
        if (!$id) {
            return $arrData;
        }

        $query = \DB::table($this->table)->whereIn($this->primaryKey, $id);

        if (!$isCache && !empty($options['select'])) {
            $query->select($options['select']);
        }
        $data = $query->get();
        if ($data->isEmpty()) {
            return $arrData;
        }
        ///////
        if ($isCache) {
            $data = $data->keyBy($this->primaryKey)->all();
            $this->cache()->update($data);
            if (!empty($options['select'])) {
                $data = \Arr::map($data, function ($value, $key) use ($options) {
                    return \Arr::only($value, $options['select']);
                });
            }
            $data = array_values($data);
        } else {
            $data = $data->toArray();
        }
        $arrData = $arrData + $data;
        unset($data);
        return $arrData;
    }
    public function detail($id, $options = [])
    {
        if (is_array($id)) {
            return $this->details($id, $options);
        }
        if (!empty($this->idAutoIncrement)) {
            $id = (int) $id;
        }
        //////// GET CACHE ////////
        $isCache = (!empty($this->is_cache) && empty($options['reset_cache'])) ? 1 : 0;
        if ($isCache) {
            $data = $this->cache()->detail($id, $options);
            if ($data) {
                return $data;
            }
        }
        /////// SELECT ///

        $query = \DB::table($this->table)->where($this->primaryKey, $id);
        // GET DATA WITHOUT CACHE + WITH SELECT
        if (!empty($options['select'])) {
            $options['select'] = is_array($options['select']) ? $options['select'] : explode(',', $options['select']);
            array_push($options['select'], $this->primaryKey);
            if (!$isCache) {
                $query->select($options['select']);
            }
        }
        $data = $query->first();
        if ($isCache) {
            $this->cache()->update($id, $data);
            if (!empty($options['select'])) {
                $data = \Arr::only($data, $options['select']);
            }
        }
        return $data;
    }
    public function delete($id)
    {
        return $this->remove($id);
    }
    public function remove($id)
    {
        if (!empty($this->idAutoIncrement)) {
            $id = (int) $id;
        } else {
            $arrId = $this->filter([$this->primaryKey => $id]);
            $id = $arrId[$this->primaryKey];
        }

        $result = \DB::table($this->table)->where($this->primaryKey, $id)->delete();
        if (!empty($this->is_cache)) {
            $this->cache()->delete($id);
        }
        return $result;
    }
    public function filter($params)
    {
        if (empty($this->casts) || empty($params)) {
            return $params;
        }
        if (is_first_key_operator($params)) {
            foreach ($params as $k => $param) {
                $params[$k] = $this->filter($param);
            }
        }
        ///// ADD primaryKey to INT
        if (!empty($this->idAutoIncrement)) {
            $this->casts['integer'][] = $this->primaryKey;
        }
        //$params = \Arr::dot($params);

        $result = [];
        $arrKeys = array_keys($params);
        foreach ($this->casts as $formatType => $v) {
            $v = array_unique($v);
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
                        if ($isRegex == 0) {
                            if ((!is_array($params[$key]))) {
                                $params[$key] = (is_numeric($params[$key])) ? (int) $params[$key] : strtotime($params[$key]);
                            } else {
                                if (array_keys($params[$key]) !== range(0, count($params[$key]) - 1)) { // nhieu chieu
                                    foreach ($params[$key] as $k => $v) {
                                        if (is_null($v)) {
                                            unset($params[$key][$k]);
                                            continue;
                                        }
                                        if (is_numeric($v)) {
                                            $params[$key][$k] = (int) $v;
                                        } else {
                                            // ktra xem co phai thoi gian ko
                                            if (strpos($v, ':') === false && in_array($k, ['lte', 'lt'])) {
                                                $v .= ' 23:59:59';
                                            }
                                            $params[$key][$k] = strtotime($v);
                                        }
                                    }
                                } else {
                                    $params[$key] = array_map(function ($item) {
                                        return (is_numeric($item)) ? (int) $item : strtotime($item);
                                    }, $params[$key]);
                                }
                            }
                        } else {
                            foreach ($data as $k => $dt) {
                                $kk = preg_replace('/(\.|^)(\d+)(\.|$)/', '.', $k);
                                $kk = trim($kk, '.');
                                //var_dump($arr[0].'.'.$k,$key);
                                if ($arr[0] . '.' . $kk == $key) {
                                    $data[$k] = (is_numeric($dt)) ? (int) $dt : strtotime($dt);
                                }
                            }
                            $data = \Arr::undot($data);
                            $params[$arr[0]] = $data;
                        }

                        break;
                }
            }
        }
        return $params;
    }
    public function aggregate($aggregate)
    {
        foreach ($aggregate as $i => $aggs) {
            foreach ($aggs as $j  => $a) {
                if ($j == '$match') {
                    $aggregate[$i][$j] = $this->getMatch($a);
                }
                if (!$aggregate[$i][$j]) {
                    unset($aggregate[$i][$j]);
                }
            }
            if (!$aggregate[$i]) {
                unset($aggregate[$i]);
            }
        }
        $aggregate = array_values($this->replaceKey($aggregate));
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
    /**
     * @author: namtq
     * @todo: load class cache from cachePath
     */
    public function cache($cachePath = '')
    {
        $className = ($cachePath) ? $cachePath : $this->cachePath;
        return \Microservices::loadCache($className);
    }
}
