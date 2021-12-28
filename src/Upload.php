<?php
namespace Microservices;

class Upload
{
    
    protected $_url;
    protected $_hash_secret;
    public function __construct() {
        $this->_url = env('SERVICE_UPLOAD_URL');
        $this->_hash_secret = env('UPLOAD_HASH_SECRET');
    }


    public function moveFiles($files_path = null) {
        if(!$files_path) {
            return false;
        }
        if(is_array($files_path)) {
            $hash = hash('sha256',implode('',$files_path).$this->_hash_secret);
        } else {
            $hash = hash('sha256',$files_path.$this->_hash_secret);
        }
        
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->patch( $this->_url.'/moves',[
            'files_path' => $files_path,
            'hash' => $hash
        ]);
        if ($response->successful()) {
            return $response->json();
        }

        \Log::error($response->body());
        return false;
    }

    public function moveAndDeleteFiles($params = []) {
      
        $newParams =  \Arr::only($params, ['paths_move','paths_delete']);
        
        if(count($newParams) === 0) {
            return false;
        }

        $newParams['paths_delete'] =  $newParams['paths_delete'] ?? [];
        $newParams['paths_move'] =  $newParams['paths_move'] ?? [];

        if(!is_array($newParams['paths_move']) || !is_array($newParams['paths_delete'])){
            return false;
        }
     
        if(count($newParams['paths_move']) === 0 && count($newParams['paths_delete']) === 0) {
            return false;
        }

        $fn = function (\Illuminate\Http\Client\Pool $pool) use ($newParams) {
            if(count($newParams['paths_delete']) > 0) {
                $data = ['files_path' => $newParams['paths_delete'], 'hash' => hash('sha256',implode('',$newParams['paths_delete']).$this->_hash_secret) ];
                $arrayPools[] = $pool->as('delete')->withToken(env('API_MICROSERVICE_TOKEN',''))->delete($this->_url.'/deletes',$data);
            }
            if(count($newParams['paths_move']) > 0) {
                $data = ['files_path' => $newParams['paths_move'], 'hash' => hash('sha256', implode('',$newParams['paths_move']).$this->_hash_secret) ];
                $arrayPools[] = $pool->as('move')->withToken(env('API_MICROSERVICE_TOKEN',''))->patch($this->_url.'/moves',$data);
            }
           
            return $arrayPools;
        };
        
        
        $responses = \Illuminate\Support\Facades\Http::pool($fn);
        $result = [];
        if(isset($responses['delete'])) {
            if($responses['delete']->successful()) {
                $result['delete'] = true;
            } else {
                \Log::error($responses['delete']->body());
                $result['delete'] = false;
            }
        }
        

        if(isset($responses['move'])) {  
            if($responses['move']->successful()) {
                    $result['move'] = true;
            } else {
                \Log::error($responses['move']->body());
                $result['move'] = false;
            }
        }

        return $result;

        
    }
}