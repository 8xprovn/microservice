<?php

namespace Microservices\models\Support;

class Comments extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = [])
    {
        $this->_url = env('API_MICROSERVICE_URL_V2') . '/support/comment';
        
    }
    public function count($params)
    {
        $_url = env('API_MICROSERVICE_URL_V2') . '/support/comment/count';
        $result = $this->safeGet($_url, $params, (string) $this->getToken());
        return $result === false ? 0 : $result;
    }

    public function getComment($filter)
    {
        $_url = env('API_MICROSERVICE_URL_V2') . '/support/comment/render_comment';
        $results = $this->safeGet($_url, $filter, (string) $this->getToken());
        if ($results === false || !is_array($results)) {
            return '';
        }
        return $results['html'] ?? '';
    }
}
