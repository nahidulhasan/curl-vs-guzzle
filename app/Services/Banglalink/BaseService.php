<?php

namespace App\Services\Banglalink;

use App\Exceptions\BLServiceException;
use App\Exceptions\CurlRequestException;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class BaseService
{
    protected const IDP_TOKEN_REDIS_KEY = "IDP_TOKEN";

    /**
     * Return BL API Host
     *
     * @return mixed
     */
    protected function getHost()
    {
        return env('BL_API_HOST');
    }

    /**
     * Make the header array with authentication.
     *
     * @return array
     */
    protected function makeHeader()
    {
        $client_token = Redis::get(self::IDP_TOKEN_REDIS_KEY);
        $customer_token = app('request')->bearerToken();

        $header = [
            'Accept: application/vnd.banglalink.apihub-v1.0+json',
            'Content-Type: application/vnd.banglalink.apihub-v1.0+json',
            'client_authorization:' . $client_token,
            'customer_authorization:' . $customer_token
        ];

        return $header;
    }


    /**
     * Make CURL request for GET request.
     *
     * @param  string  $url
     * @param  array  $body
     * @param  array  $headers
     * @param  bool  $skip_service_exception
     * @return array
     * @throws BLServiceException
     * @throws CurlRequestException
     */
    protected function get($url, $body = [], $headers = null, $skip_service_exception = false)
    {
        return $this->makeMethod('get', $url, $body, $headers, $skip_service_exception);
    }

    /**
     * Make CURL request for POST request.
     *
     * @param  string  $url
     * @param  array  $body
     * @param  array  $headers
     * @param  bool  $skip_service_exception
     * @return array
     * @throws BLServiceException
     * @throws CurlRequestException
     */
    protected function post($url, $body = [], $headers = null, $skip_service_exception = false)
    {
        return $this->makeMethod('post', $url, $body, $headers, $skip_service_exception);
    }

    /**
     * Make CURL request for PUT request.
     *
     * @param  string  $url
     * @param  array  $body
     * @param  array  $headers
     * @return array
     * @throws BLServiceException
     * @throws CurlRequestException
     */
    protected function put($url, $body = [], $headers = [])
    {
        return $this->makeMethod('put', $url, $body, $headers);
    }

    /**
     * Make CURL request for patch request.
     *
     * @param  string  $url
     * @param  array  $body
     * @param  array  $headers
     * @return array
     * @throws BLServiceException
     * @throws CurlRequestException
     */
    protected function patch($url, $body = [], $headers = [])
    {
        return $this->makeMethod('patch', $url, $body, $headers);
    }


    /**
     * @param $url
     * @param  array  $body
     * @param  array  $headers
     * @return array
     * @throws BLServiceException
     * @throws CurlRequestException
     */
    protected function delete($url, $body = [], $headers = [])
    {
        return $this->makeMethod('delete', $url, $body, $headers);
    }

    /**
     * Make CURL request for a HTTP request.
     *
     * @param  string  $method
     * @param  string  $url
     * @param  array  $body
     * @param  array  $headers
     * @param  bool  $skip_service_exception
     * @return array
     * @throws BLServiceException
     * @throws CurlRequestException
     */
    protected function makeMethod(
        $method,
        $url,
        $body = [],
        $headers = null,
        $skip_service_exception = false
    ) {
        //$start_time = microtime(true);

        $ch = curl_init();
        $headers = $headers ?: $this->makeHeader();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        static::makeRequest($ch, $url, $body, $headers);
        $result = curl_exec($ch);

        //$end_time = microtime(true);

        $curl_info = curl_getinfo($ch);


        if ($result != '' && !$result) {
            throw new CurlRequestException(curl_getinfo($ch));
        }
        $httpCode = $curl_info['http_code'];

        if ($httpCode >= 500 && !$skip_service_exception) {
            throw new BLServiceException($result);
        }

        /*        try {
                    $this->logToApiPerformance([
                        'url' => $curl_info['url'],
                        'start_time' => $start_time,
                        'end_time' => $end_time,
                        'response_time' => $end_time - $start_time,
                        'response' => $result
                    ]);
                } catch (Exception $e) {
                }*/

        return ['response' => $result, 'status_code' => $httpCode];
    }


    /**
     * Make CURL object for HTTP request verbs.
     *
     * @param  curl_init() $ch
     * @param  string  $url
     * @param  array  $body
     * @param  array  $headers
     * @return string
     */
    protected function makeRequest($ch, $url, $body, $headers)
    {
        $url = $this->getHost() . $url;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }

    protected function logToApiPerformance($payload)
    {
        Log::channel('blServicePerformanceLog')->info($payload);
    }
}
