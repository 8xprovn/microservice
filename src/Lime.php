<?php
namespace Microservices;

class Lime
{

    public function __construct() {
        /* Remind to download and put files in https://github.com/weberhofer/jsonrpcphp at the good place */
        $this->_rpcUrl="https://demo-qlcl.imap.edu.vn/index.php/admin/remotecontrol";
        $this->_rpcUser="admin";
        $this->_rpcPassword="hoanguyen";

        $this->_lsJSONRPCClient = new \org\jsonrpcphp\jsonRPCClient($this->_rpcUrl);
    }

    public function sessionKey(){
        $lsJSONRPCClient = $this->_lsJSONRPCClient;
        $sessionKey= $lsJSONRPCClient->get_session_key($this->_rpcUser, $this->_rpcPassword);
        return $sessionKey;
    }

    public function lime($name){
        $lsJSONRPCClient = $this->_lsJSONRPCClient;
        $sessionKey = $this->sessionKey();
        $response = $lsJSONRPCClient->$name($sessionKey,null);
        return $response;
    }

    public function copy_survey($iSurveyID_org, $sNewname){
        $lsJSONRPCClient = $this->_lsJSONRPCClient;
        $sessionKey = $this->sessionKey();
        $response = $lsJSONRPCClient->copy_survey($sessionKey, $iSurveyID_org, $sNewname);
        return $response;
    }

    public function get_responses($iSurveyID, $params = array()){
        $lsJSONRPCClient = $this->_lsJSONRPCClient;
        $sessionKey = $this->sessionKey();
        $response = $lsJSONRPCClient->export_responses($sessionKey, $iSurveyID, 'json');
        return json_decode(base64_decode($response), TRUE);
    }
}