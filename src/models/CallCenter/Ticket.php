<?php

namespace Microservices\models\CallCenter;

use Illuminate\Support\Arr;

class Ticket extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/call-center/ticket';
        
    }

    public function close($id, $params)
    {
        if (!empty($this->only['update'])) {
            $params = \Arr::only($params, $this->only['update']);
        }
        if (!empty($this->idAutoIncrement)) {
            $id = (int) $id;
        }
        $url = $this->_url . '/' . $id . '/close';
        $result = $this->safePost($url, $params, (string) $this->getToken());
        return $result === false ? false : $result;
    }

    public function comment($id, $params)
    {
        if (!empty($this->only['update'])) {
            $params = \Arr::only($params, $this->only['update']);
        }
        if (!empty($this->idAutoIncrement)) {
            $id = (int) $id;
        }
        $url = $this->_url . '/' . $id . '/comment';
        $result = $this->safePost($url, $params, (string) $this->getToken());
        return $result === false ? false : $result;
    }

    public function assign($id, $params)
    {
        if (!empty($this->only['update'])) {
            $params = \Arr::only($params, $this->only['update']);
        }
        if (!empty($this->idAutoIncrement)) {
            $id = (int) $id;
        }
        $url = $this->_url . '/' . $id . '/assign';
        $result = $this->safePost($url, $params, (string) $this->getToken());
        return $result === false ? false : $result;
    }

    public function action($id, $params)
    {
        if (!empty($this->only['update'])) {
            $params = \Arr::only($params, $this->only['update']);
        }
        if (!empty($this->idAutoIncrement)) {
            $id = (int) $id;
        }
        $url = $this->_url . '/' . $id . '/action';
        $result = $this->safePost($url, $params, (string) $this->getToken());
        return $result === false ? false : $result;
    }

    public function storeFollower($id, $params)
    {
        if (!empty($this->only['update'])) {
            $params = \Arr::only($params, $this->only['update']);
        }
        if (!empty($this->idAutoIncrement)) {
            $id = (int) $id;
        }
        $url = $this->_url . '/store-follower';
        $result = $this->safePost($url, $params, (string) $this->getToken());
        return $result === false ? false : $result;
    }

    public function updateParamProcess($id, $params)
    {
        if (!empty($this->only['update'])) {
            $params = \Arr::only($params, $this->only['update']);
        }
        if (!empty($this->idAutoIncrement)) {
            $id = (int) $id;
        }
        $url = $this->_url . '/' . $id . '/update-params';
        $result = $this->safePost($url, $params, (string) $this->getToken());
        return $result === false ? false : $result;
    }

    public function destroyFollower($id, $params)
    {
        if (!empty($this->only['update'])) {
            $params = \Arr::only($params, $this->only['update']);
        }
        if (!empty($this->idAutoIncrement)) {
            $id = (int) $id;
        }
        $url = $this->_url . '/' . $id . '/destroy-follower';
        $result = $this->safePost($url, $params, (string) $this->getToken());
        return $result === false ? false : $result;
    }

    public function cancel($id, $params)
    {
        if (!empty($this->only['update'])) {
            $params = \Arr::only($params, $this->only['update']);
        }
        if (!empty($this->idAutoIncrement)) {
            $id = (int) $id;
        }
        $url = $this->_url . '/' . $id . '/cancel';
        $result = $this->safePost($url, $params, (string) $this->getToken());
        return $result === false ? false : $result;
    }
}
