<?php

namespace Microservices\models;

use Microservices\models\BaseModel;

class Export extends BaseModel
{
    private $client;
    function login() {
        if ($this->client) {
            return $this->client;
        }
        $arr_credentitals = config("services.google_credential_export");
        $credentials['credentials'] = json_decode($arr_credentitals[array_rand($arr_credentitals)], true);
        $client = new \Google_Client($credentials,true);
        $client->useApplicationDefaultCredentials();
        $client->setApplicationName('Google Sheets IMAP ERP');
        $client->setScopes([\Google_Service_Sheets::DRIVE,\Google_Service_Drive::DRIVE]);
        $this->client = $client;
        return $client;
    }
    function createFile($options) {
        if(!empty($options['type']) && $options['type'] == 'schedule'){
            $fileId = $this->checkFileInFolder($options['folderId']??'', $options['name'].'-'.date('Y-m'));
            if(empty($fileId)){         //Tạo mới file
                $fileId = $this->createFileAuto(['name' => $options['name'], 'folderId' => $options['folderId']??'']);
            }else{                      //Đã tồn tại
                $this->clearSheet($fileId);      //Clear file
            }
            return $fileId;
        }
        $options['name'] .= '-'.$options['created_by'];
        $client = $this->login();
        $serviceDrive = new \Google_Service_Drive($client);
        $createFiles = new \Google_Service_Drive_DriveFile([
            'name' => ($options['name']??'file') . '-'.date('Y-m-d').microtime(true),
            'parents' => ['1zT0JzAHTXbje9Z9rRdRlX-EEgjBV1yov'],
            'mimeType' => 'application/vnd.google-apps.spreadsheet',
        ]);
        $createdData = $serviceDrive->files->create($createFiles,['fields' => 'id']);
        $arrPermissions = [
            'type' => 'anyone',
            'role' => 'reader',
            'withLink' => true,
        ];
        $permissions = new \Google_Service_Drive_Permission($arrPermissions);
        $serviceDrive->permissions->create($createdData->id,$permissions);
        return $createdData->id;
    }
    function writeData($file, $data = array())
    {
        $client = $this->login();

        $body = new \Google_Service_Sheets_ValueRange([
            'values' => $data
        ]);
        $params = [
            'valueInputOption' => 'USER_ENTERED'
        ];
        $service = new \Google_Service_Sheets($client);
        // update row
        $response = $service->spreadsheets_values->append($file, 'Sheet1!A:A', $body,$params);
        return $response;
    }

    function checkFileInFolder($folderId, $fileName) {
        if(empty($folderId)){
            $folderId = '1vn9O9GYCf1IQXM1ybX2lkqrSUexXHr0b';
        }
        $client = $this->login();
        $serviceDrive = new \Google_Service_Drive($client);

        // Tạo truy vấn để tìm file trong thư mục
        $query = sprintf("name = '%s' and '%s' in parents and trashed = false", $fileName, $folderId);
        try {
            $response = $serviceDrive->files->listFiles(array(
                'q' => $query,
                'spaces' => 'drive',
                'fields' => 'files(id, name)'
            ));
            // Kiểm tra kết quả
            if (count($response->files) > 0) {
                return $response->files[0]->id; // File tồn tại
            } else {
                return false;   // File không tồn tại
            }
        } catch (\Google_Service_Exception $e) {
            \Log::error('Error: ' . $e->getMessage());
            return false;
        }
    }

    function clearSheet($spreadsheetId, $range = 'Sheet1!A:ZZ'){
        $client = $this->login();
        $service = new \Google_Service_Sheets($client);
        $clearRequest = new \Google_Service_Sheets_ClearValuesRequest();
        try {
            $response = $service->spreadsheets_values->clear($spreadsheetId, $range, $clearRequest);
            return $response;
        } catch (Exception $e) {
            \Log::error('Error: ' . $e->getMessage());
        }
    }

    function createFileAuto($options) {
        $client = $this->login();
        $serviceDrive = new \Google_Service_Drive($client);
        $createFiles = new \Google_Service_Drive_DriveFile([
            'name' => ($options['name']??'file') . '-'.date('Y-m'),
            'parents' => [$options['folderId' ??'1vn9O9GYCf1IQXM1ybX2lkqrSUexXHr0b']],
            'mimeType' => 'application/vnd.google-apps.spreadsheet',
        ]);

        $createdData = $serviceDrive->files->create($createFiles,['fields' => 'id']);
        $arrPermissions = [
            'type' => 'anyone',
            'role' => 'reader',
            'withLink' => true,
        ];
        $permissions = new \Google_Service_Drive_Permission($arrPermissions);
        $serviceDrive->permissions->create($createdData->id,$permissions);
        return $createdData->id;
    }
}