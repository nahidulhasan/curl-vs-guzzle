<?php

namespace App\Services\Bonus;

use App\Exceptions\IdpException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;


/**
 * Class IDMIntegrationService
 * @package App\Services\Bonus
 */
class IDMIntegrationService
{

    /**
     * Get Host from env file
     *
     * @return string
     */
    public static function getHost()
    {
        return env('IDM_HOST');
    }

    /**
     * Get Token from env file
     *
     * @return string
     */
    public static function getToken()
    {
         return env('IDM_CLIENT_TOKEN');
    }


    /**
     * Send request for user registration
     *
     * @return string
     * @throws IdpException
     */
    public static function getAccessToken()
    {
        $data_param = "grant_type=client_credentials";

        $response = static::post('/api/token', $data_param);

        $response_data = json_decode($response['response']);

        if (isset($response_data->access_token)) {
            return $response_data->access_token;
        }

        return "RESTFGG8Q";
    }



    /**
     * Make the header array with authentication.
     *
     * @return array
     */
    private static function makeHeader()
    {

        $client_id = env('IDM_CLIENT_ID');
        $client_secret = env('IDM_CLIENT_SECRET');

        $header = [
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic '. base64_encode("$client_id:$client_secret")
        ];

        return $header;
    }


    /**
     * Make CURL request for GET request.
     *
     * @param string $url
     * @param array $body
     * @param array $headers
     * @return array
     * @throws IdpException
     */
    public static function get($url, $body = [], $headers = null)
    {
        return static::makeMethod('get', $url, $body, $headers);
    }

    /**
     * Make CURL request for POST request.
     *
     * @param string $url
     * @param array $body
     * @param array $headers
     * @return array
     * @throws IdpException
     */
    public static function post($url, $body = [], $headers = null)
    {
        return static::makeMethod('post', $url, $body, $headers);
    }

    /**
     * Make CURL request for PUT request.
     *
     * @param string $url
     * @param array $body
     * @param array $headers
     * @return array
     * @throws IdpException
     */
    public static function put($url, $body = [], $headers = [])
    {
        return static::makeMethod('put', $url, $body, $headers);
    }

    /**
     * Make CURL request for DELETE request.
     *
     * @param string $url
     * @param array $body
     * @param array $headers
     * @return array
     * @throws IdpException
     */
    public static function delete($url, $body = [], $headers = [])
    {
        return static::makeMethod('delete', $url, $body, $headers);
    }

    /**
     * Make CURL request for a HTTP request.
     *
     * @param string $method
     * @param string $url
     * @param array $body
     * @param array $headers
     * @return array
     * @throws IdpException
     */
    private static function makeMethod($method, $url, $body = [], $headers = null)
    {
        $ch = curl_init();
        $headers = $headers ?: static::makeHeader();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        static::makeRequest($ch, $url, $body, $headers);

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        $httpCode = $info['http_code'];

        if ($info['http_code'] == 401) {
            static::makeRequest($ch, $url, $body, $headers);
            $result = curl_exec($ch);
        }

        if ($info['http_code'] >= 500) {
            throw new IdpException($result);
        }

        curl_close($ch);
        return ['response' => $result, 'status_code' => $httpCode];
    }


    /**
     * Make CURL object for HTTP request verbs.
     *
     * @param curl_init() $ch
     * @param string $url
     * @param array $body
     * @param array $headers
     * @return string
     */
    private static function makeRequest($ch, $url, $body, $headers)
    {
        $url = static::getHost() . $url;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }
}
