<?php
namespace Microservices;

class Lime
{

    public function __construct() {
        /* Remind to download and put files in https://github.com/weberhofer/jsonrpcphp at the good place */
        $this->_rpcUrl=env('LIME_URL','');
        $this->_rpcUser=env('LIME_USER','');
        $this->_rpcPassword=env('LIME_PASSWORD','');

        $this->_lsJSONRPCClient = new \org\jsonrpcphp\JsonRPCClient($this->_rpcUrl);
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

    //Active survey
    public function activate_survey($iSurveyID){
        $lsJSONRPCClient = $this->_lsJSONRPCClient;
        try {
            $sessionKey = $this->sessionKey();
            $response = $lsJSONRPCClient->activate_survey($sessionKey, $iSurveyID);
            return $response;
        }catch (\Throwable $e){
            $result =  ['status' => 'error', 'message' => $e->getMessage()];
            return $result;
        }
    }

    public function get_responses($iSurveyID, $params = array()){
        $lsJSONRPCClient = $this->_lsJSONRPCClient;
        try {
            $sessionKey = $this->sessionKey();
            $response = $lsJSONRPCClient->export_responses($sessionKey, $iSurveyID, 'json', '', 'complete', 'full', 'long');
            if(is_array($response) && !empty($response['status'])){
                $result =  ['status' => 'error', 'message' => $response['status']];
                return $result;
            }
            return json_decode(base64_decode($response), TRUE);
        }catch (\Throwable $e){
            $result =  ['status' => 'error', 'message' => $e->getMessage()];
            return $result;
        }
    }

    public function get_summary($iSurveyID){
        $lsJSONRPCClient = $this->_lsJSONRPCClient;
        $sessionKey = $this->sessionKey();
        $response = $lsJSONRPCClient->get_summary($sessionKey, $iSurveyID, 'all');
        return $response;
    }

    public function add_participants($iSurveyID, $aParticipantData){
        $lsJSONRPCClient = $this->_lsJSONRPCClient;
        try {
            $sessionKey = $this->sessionKey();
            $response = $lsJSONRPCClient->add_participants($sessionKey, $iSurveyID, $aParticipantData);
            return $response;
        }catch (\Throwable $e){
            $result =  ['status' => 'error', 'message' => $e->getMessage()];
            return $result;
        }
    }

    public function activate_tokens($iSurveyID){
        $lsJSONRPCClient = $this->_lsJSONRPCClient;
        try {
            $sessionKey = $this->sessionKey();
            $result = $lsJSONRPCClient->activate_tokens($sessionKey, $iSurveyID);
            return $result;
        }catch (\Throwable $e){
            $result =  ['status' => 'error', 'message' => $e->getMessage()];
            return $result;
        }
    }

    public function export_responses_by_token($iSurveyID, $sToken){
        $lsJSONRPCClient = $this->_lsJSONRPCClient;
        try {
            $sessionKey = $this->sessionKey();
            $response = $lsJSONRPCClient->export_responses_by_token($sessionKey, $iSurveyID, 'json', $sToken, '', 'complete', 'full', 'long');
            if(is_array($response) && !empty($response['status'])){
                $result =  ['status' => 'error', 'message' => $response['status']];
                return $result;
            }
            return json_decode(base64_decode($response), TRUE);
        }catch (\Throwable $e){
            $result =  ['status' => 'error', 'message' => $e->getMessage()];
            return $result;
        }
    }
}