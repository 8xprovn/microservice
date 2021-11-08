<?php
namespace Microservices;


class Inventory
{
    protected $_url;
    public function __construct() {
        $this->_url = env('API_MICROSERVICE_URL').'/inventory';
    }

     public function getProducts($params =[])
     {
         $whereArr = \Arr::only($params, ['product_id', 'status',  'limit', 'offset']);
         $filter = [];
         $limit = isset($whereArr['limit']) && $whereArr['limit'] > 0 ? $whereArr['limit'] : 200;
         $offset = isset($whereArr['offset']) && $whereArr['offset'] > 0 ? $whereArr['offset'] : 0;
         foreach($whereArr as $k => $v){
             if (is_null($v)) continue;
             switch ($k) {
                 default:
                     if (is_array($v)) {
                         $filter[$k] = ['inq' => $v];
                     }
                     else if($v != 'limit' && $v != 'offset') {
                         $filter[$k] = ['eq' => $v];
                     }
                     break;
             }
         }

        $newFilter = [
            'limit' => $limit,
            'offset' => $offset
        ];

        if(count($filter) > 0) {
            $newFilter = [
                'limit' => $limit,
                'offset' => $offset,
                'where' => $filter,
            ];
        }
        

         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/products',['filter' => json_encode($newFilter)]);
         
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
    }
    public function getProductDetail($id) {
        //var_dump(['filter' => json_encode(['where' => $filter])]); die;
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/products/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getStores($params =[])
    {
        $whereArr = \Arr::only($params, ['product_id', 'status',  'limit', 'offset']);
        $filter = [];
        $limit = isset($whereArr['limit']) && $whereArr['limit'] > 0 ? $whereArr['limit'] : 200;
        $offset = isset($whereArr['offset']) && $whereArr['offset'] > 0 ? $whereArr['offset'] : 0;
        foreach($whereArr as $k => $v){
            if (is_null($v)) continue;
            switch ($k) {
                default:
                    if (is_array($v)) {
                        $filter[$k] = ['inq' => $v];
                    }
                    else if($v != 'limit' && $v != 'offset') {
                        $filter[$k] = ['eq' => $v];
                    }
                    break;
            }
        }

       $newFilter = [
           'limit' => $limit,
           'offset' => $offset
       ];

       if(count($filter) > 0) {
           $newFilter = [
               'limit' => $limit,
               'offset' => $offset,
               'where' => $filter,
           ];
       }

        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/stores',['filter' => json_encode($newFilter)]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
   }
   
    public function getStoreDetail($id) {
        //var_dump(['filter' => json_encode(['where' => $filter])]); die;
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/stores/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }


    public function getInventories($params =[])
    {
        $whereArr = \Arr::only($params, ['inventory_id', 'store_id',  'limit', 'offset']);
        $filter = [];
        $limit = isset($whereArr['limit']) && $whereArr['limit'] > 0 ? $whereArr['limit'] : 200;
        $offset = isset($whereArr['offset']) && $whereArr['offset'] > 0 ? $whereArr['offset'] : 0;
        foreach($whereArr as $k => $v){
            if (is_null($v)) continue;
            switch ($k) {
                default:
                    if (is_array($v)) {
                        $filter[$k] = ['inq' => $v];
                    }
                    else if($v != 'limit' && $v != 'offset') {
                        $filter[$k] = ['eq' => $v];
                    }
                    break;
            }
        }

       $newFilter = [
           'limit' => $limit,
           'offset' => $offset
       ];

       if(count($filter) > 0) {
           $newFilter = [
               'limit' => $limit,
               'offset' => $offset,
               'where' => $filter,
           ];
       }

        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/inventories',['filter' => json_encode($newFilter)]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
   }
   
    public function getInventoryDetail($id) {
        //var_dump(['filter' => json_encode(['where' => $filter])]); die;
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/inventories/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    

}