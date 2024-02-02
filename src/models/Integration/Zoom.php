<?php

namespace Microservices\models\Integration;

use Illuminate\Support\Arr;

class Zoom
{
    private $_url = 'https://api.zoom.us/v2';
    public function __construct($options = [])
    {
    }

    function getAccessToken()
    {
        $account_id = env('ZOOM_ACCOUNT_ID', '');
        $clientId = env('ZOOM_CLIENT_ID', '');
        $clientSecret = env('ZOOM_CLIENT_SECRET', '');

        $key = "zoom_access_token_" . $clientId;
        $token = \Cache::get($key, '');
        if (!empty($token)) {
            return $token;
        }
        $query = http_build_query($params = [
            'grant_type' => 'account_credentials',
            'account_id' => $account_id
        ]);
        $response = \Http::withBasicAuth($clientId, $clientSecret)
            ->withHeaders([
                'ContentType' => 'application/x-www-form-urlencoded'
            ])
            ->post('https://zoom.us/oauth/token?' . $query);
        $result = $response->json();
        if (!empty($result['access_token'])) {
            \Cache::put($key, $result['access_token'], 1800);
            return $result['access_token'];
        }
        return false;
    }
    public function getMeetingByEmail($email)
    {
        $url = "{$this->_url}/users/{$email}";
        $token = $this->getAccessToken();
        $response = \Http::withToken($token)->get($url);

        if ($response->status() == 200) {
            return $response->json();
        }
        return [];
    }
    public function deleteRecordByMeetingId($meetingId) //hàm xóa link zoom
    {
        $url = "{$this->_url}/meetings/{$meetingId}/recordings";
        $token =  $this->getAccessToken();
        $response = \Http::withToken($token)->delete($url, [
            'action' => 'trash'
        ]);
        if ($response->status() == 200 || $response->status() == 204) {
            return true;
        }
        return false;
    }

    public function getRecordingByEmail($email, $param)
    {
        $url = "{$this->_url}/users/{$email}/recordings";
        $token =  $this->getAccessToken();
        $response = \Http::withToken($token)->get($url, $param);
        if ($response->status() == 200) {
            return $response->json();
        }
        return [];
    }
    public function getMeeting($meetingId) //hàm lấy link zoom
    {
        $url = "{$this->_url}/meetings/{$meetingId}";
        $token =  $this->getAccessToken();
        $response = \Http::withToken($token)->get($url);
        if ($response->status() == 200) {
            return $response->json();
        }
        return [];
    }

    public function syncLicense($email, $type = 1) // type (2 = licensed : 1 = basic)
    {
        $token = $this->getAccessToken();
        $response = \Http::withToken($token)->patch("{$this->_url}/users/{$email}", ['type' => $type]);
        if ($response->successful()) {
            return ['status' => $response->status(), 'data' => $response->json()];
        }
        throw new \Exception($response->body());
        return ['status' => $response->status(), 'data' => $response->body()]; 
    }
    public function addUser($data = []) // type (2 = licensed : 1 = basic)
    {
        if (empty($data['type'])) $data['type'] = 1;
        $token = $this->getAccessToken();
        $response = \Http::withToken($token)->post("https://api.zoom.us/v2/users", [
            "action" => "create",
            "user_info" => $data
        ]); 
        
        if ($response->successful()) {
            return ['status' => $response->status(), 'data' => $response->json()];
        }
        // throw new \Exception($response->body());
        return ['status' => $response->status(), 'data' => $response->json()];
    }
}
