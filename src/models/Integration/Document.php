<?php

namespace Microservices\models\Integration;


class Document
{
    private $token;
    public function __construct($options = [])
    {
        $this->token = env('API_MICROSERVICE_TOKEN');
    }

    function create($data)
    {
        $url = 'https://s.ebomb.edu.vn/document'; // Đường dẫn đến tệp PHP thuần

        try {
            $response = \Http::post($url, [
                'data_replace' => $data['data_replace'],
                'url' => $data['url'],
                'name_file' => $data['name_file'],
                'token' => $this->token,
            ]);

            if ($response->ok()) {
                // Đặt header tải file xuống
                return [
                    'status' => 'success',
                    'response' => response($response->body(), 200)
                        ->header('Content-Type', $response->header('Content-Type'))
                        ->header('Content-Disposition', $response->header('Content-Disposition')),
                ];
            } else {
                // Trả về lỗi chi tiết
                return [
                    'status' => 'error',
                    'message' => 'Lỗi khi gọi API: ' . $response->body(),
                    'http_status' => $response->status(),
                    'error_body' => $response->body(),
                ];
            }
        } catch (\Exception $e) {
            // Bắt các lỗi ngoại lệ và trả về lỗi chi tiết
            return [
                'status' => 'error',
                'message' => 'Ngoại lệ khi gọi API: ' . $e->getMessage(),
                'exception' => $e->getTraceAsString(), // Debug thông tin stack trace nếu cần
            ];
        }
    }
}
