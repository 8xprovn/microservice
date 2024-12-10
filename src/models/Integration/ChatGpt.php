<?php

namespace Microservices\models\Integration;


class ChatGpt
{
    public function __construct($options = []) {

    }

    public function mark($params) {
        $input = $params;
        $validator = \Validator::make($input, [
            'topic' => 'required',
            'content' => 'required'
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            //pass validator errors as errors object for ajax response
            return ['status'=> 'error', 'message'=>$validator->errors()->first()];
        }
        $apiKey = env('KEY_CHATGPT') ?? '';;
        $contentData = [
            'model' => 'gpt-4o-mini',  // Hoặc 'gpt-4' tùy vào yêu cầu của bạn
            'messages' => [
                ['role' => 'system', 'content' => 'You are an IELTS examiner. score essays based on the IELTS Writing Band Descriptors. Topic:'.$input['topic']],
                ['role' => 'user', 'content' => $input['content']]
            ],
            "max_tokens" => 2000,

        ];
        //dd($contentData);
        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', $contentData);

        // Kiểm tra xem phản hồi có thành công không
        if ($response->successful()) {
            
            // Trả kết quả về cho người dùng
            $data = $response->json();
            //var_dump($data);
        } else {
            // Trả về lỗi nếu có
            $data = ['status'=> 'error', 'message' => $response->body()];
        }
        
        return $data;
        
    }
    
}
