<?php

namespace Microservices\models\Recruitment;

use Illuminate\Support\Arr;

class Calendar extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/recruitment/calendar';
        
    }
    public function scorecard($relate_id, $relate_type, $param = [])
    {
        $url = $this->_url . "/$relate_id/scorecard/$relate_type";

        $response = \Http::acceptJson()->withToken($this->getToken())->post($url, $param);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($this->_url . $response->body());
        return [];
    }
}
