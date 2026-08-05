<?php

namespace Microservices\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

use Log;

trait SafeHttpClient
{
    protected function safeGet(string $url, array $options = [], string $token, array $headers = [])
    {
        [$url, $options, $token, $headers] = $this->prepareRequest(
            $url,
            $options,
            $token,
            $headers
        );
        try {
            return Http::retry(1, 200)
                ->withOptions([
                    'connect_timeout' => 2,
                    'timeout' => 5,
                ])
                ->acceptJson()
                ->withHeaders($headers)
                ->withToken($token)
                ->get($url, $options)
                ->throw()->json();

        } catch (\Exception $e) {
            // Lỗi không mong muốn (logic, parse, v.v.)
            Log::error('Unexpected API exception', [
                'url' => $url,
                'error' => $e->getMessage(),
                'type' => get_class($e),
                'status' => method_exists($e, 'getCode') ? $e->getCode() : null,
            ]);
        }
        return false;
    }
    protected function safePost(string $url, array $options = [], string $token, array $headers = [])
    {
        [$url, $options, $token, $headers] = $this->prepareRequest(
            $url,
            $options,
            $token,
            $headers
        );
        try {
            return Http::retry(
                    2, // số lần retry
                    200, // delay (ms)
                    function ($exception, $request) {
                        // Chỉ retry nếu là lỗi kết nối (connect timeout, DNS, network)
                        return $exception instanceof ConnectionException;
                })
                ->withOptions([
                    'connect_timeout' => 2,
                    'timeout' => 10,
                ])
                ->acceptJson()
                ->withHeaders($headers)
                ->withToken($token)
                ->post($url, $options)
                ->throw()->json();
        } catch (RequestException $e) {
            if (!empty($e->response) && $e->response->status() >= 500) {
                Log::error('Post: Unexpected API exception', [
                    'url' => $url,
                    'error' => $e->getMessage(),
                    'type' => get_class($e),
                    'status' => $e->response->status(),
                ]);
            }
            return $e->response->json() ?? false;
        } catch (\Exception $e) {
            Log::error('Post: Unexpected API exception', [
                'url' => $url,
                'error' => $e->getMessage(),
                'type' => get_class($e),
                'status' => method_exists($e, 'getCode') ? $e->getCode() : null,
            ]);
        }
        return false;
    }
    protected function safePut(string $url, array $options = [], string $token, array $headers = [])
    {
        [$url, $options, $token, $headers] = $this->prepareRequest(
            $url,
            $options,
            $token,
            $headers
        );
        try {
            return Http::retry(
                    2, // số lần retry
                    200, // delay (ms)
                    function ($exception, $request) {
                        // Chỉ retry nếu là lỗi kết nối (connect timeout, DNS, network)
                        return $exception instanceof ConnectionException;
                })
                ->withOptions([
                    'connect_timeout' => 2,
                    'timeout' => 10,
                ])
                ->acceptJson()
                ->withHeaders($headers)
                ->withToken($token)
                ->put($url, $options)
                ->throw()->json();
        } catch (RequestException $e) {
            if (!empty($e->response) && $e->response->status() >= 500) {  
                Log::error('Post: Unexpected API exception', [
                    'url' => $url,
                    'error' => $e->getMessage(),
                    'type' => get_class($e),
                    'status' => $e->response->status(),
                ]); 
            } 
            return $e->response->json() ?? false;
        } catch (\Exception $e) {
            // Lỗi không mong muốn (logic, parse, v.v.)
            Log::error('Put: Unexpected API exception', [
                'url' => $url,
                'error' => $e->getMessage(),
                'type' => get_class($e),
                'status' => method_exists($e, 'getCode') ? $e->getCode() : null,
            ]);
        }
        return false;
    }
    protected function safeDelete(string $url, array $options = [], string $token, array $headers = [])
    {
        [$url, $options, $token, $headers] = $this->prepareRequest(
            $url,
            $options,
            $token,
            $headers
        );
        try {
            return Http::retry(
                    2, // số lần retry
                    200, // delay (ms)
                    function ($exception, $request) {
                        // Chỉ retry nếu là lỗi kết nối (connect timeout, DNS, network)
                        return $exception instanceof ConnectionException;
                })
                ->withOptions([
                    'connect_timeout' => 2,
                    'timeout' => 10,
                ])
                ->acceptJson()
                ->withHeaders($headers)
                ->withToken($token)
                ->delete($url, $options)
                ->throw()->json();
        } catch (RequestException $e) {
            if (!empty($e->response) && $e->response->status() >= 500) {  
                Log::error('Post: Unexpected API exception', [
                    'url' => $url,
                    'error' => $e->getMessage(),
                    'type' => get_class($e),
                    'status' => $e->response->status(),
                ]); 
            } 
            return $e->response->json() ?? false;
        } catch (\Exception $e) {
            // Lỗi không mong muốn (logic, parse, v.v.)
            Log::error('Delete: Unexpected API exception', [
                'url' => $url,
                'error' => $e->getMessage(),
                'type' => get_class($e),
                'status' => method_exists($e, 'getCode') ? $e->getCode() : null,
            ]);
        }
        return false;
    }
    private function prepareRequest(
        string $url,
        array $options,
        string $token,
        array $headers
    ): array {
        // Ví dụ sửa URL
        $urlService = env('API_MICROSERVICE_URL_V2');
        $urlLocal = env('API_MICROSERVICE_URL_INTERNAL');
        if (strpos($url, $urlService) !== false && !empty($urlLocal)) {
            $url = str_replace($urlService, $urlLocal, $url);
            /// HOST ///
            $headers['Host'] = parse_url($urlService, PHP_URL_HOST);
        }

        return [$url, $options, $token, $headers];
    }
}
