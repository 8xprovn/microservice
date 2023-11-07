<?php

namespace Microservices\models\Inventory;

class Logs extends \Microservices\models\Model
{
    protected $_url;
//    protected $is_cache = 1;

    public function __construct($options = [])
    {
        $this->_url = env('API_MICROSERVICE_URL_V2') . '/inventory/logs';
        $this->setToken($options['token'] ?? 'system');
    }

    public function getLogStatus($status, $params = [], $options = [])
    {
        try {
            $filter = [];
            foreach ($params as $k => $v) {
                if (is_null($v)) continue;
                switch ($k) {
                    default:
                        $filter[$k] = $v;
                        break;
                }
            }
            $q = $options;
            $q['filter'] = $filter;
            $response = \Http::acceptJson()->withToken($this->access_token)->get($this->_url. "-{$status}", $q);

            if ($response->successful()) {
                return $response->json();
            }
            if($response->status() == 403){
                return ['status' => 'error' , 'message'  => 'UnAuthenticated'];
            }
            \Log::error($this->_url . $response->body());
            return [];
        }catch (\Exception $ex){
            return [];
        }
    }
}
