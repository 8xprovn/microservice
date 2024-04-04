<?php

namespace Microservices\models\Support;

class Comments extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = [])
    {
        $this->_url = env('API_MICROSERVICE_URL_V2') . '/support/comment';
        $this->setToken($options['token'] ?? 'system');
    }
    public function count($params)
    {
        $_url = env('API_MICROSERVICE_URL_V2') . '/support/comment/count';
        $response = \Http::acceptJson()->withToken($this->access_token)->get($_url, $params);
        if ($response->successful()) { 
            return $response->json();
        }
        return 0;
    }

    public function getComment($filter)
    {
        $_url = env('API_MICROSERVICE_URL_V2') . '/support/comment/render_comment';
        $response = \Http::acceptJson()->withToken($this->access_token)->get($_url, $filter);
        if ($response->successful()) {
            $results = $response->json();
            return $results['html'] ?? '';
        } 
        return '';
    }
}
