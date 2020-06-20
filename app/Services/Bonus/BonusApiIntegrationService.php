<?php

namespace App\Services\Bonus;

use App\Exceptions\IdpException;
use App\Models\SignInBonusLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Class IDMIntegrationService
 * @package App\Services\Bonus
 */
class BonusApiIntegrationService
{

    /**
     * Get Host from env file
     *
     * @return string
     */
    public static function getHost()
    {
        return env('BONUS_API_HOST');
    }

    /**
     * Get Token from env file
     *
     * @return string
     * @throws IdpException
     */
    public static function getToken()
    {
        return IDMIntegrationService::getAccessToken();
    }

    /**
     * Send request for user registration
     *
     * @param $data
     * @return string
     * @throws IdpException
     */
    public static function bonusRequest($data)
    {
        return static::post('/api/v1.0/selfcare/processbonus', $data);
    }


    /**
     * Make the header array with authentication.
     *
     * @param  bool  $isAuthorizationRequired
     * @return array
     * @throws IdpException
     */
    private static function makeHeader($isAuthorizationRequired = true)
    {
        $header = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Expect: 100-continue'
        ];

        if ($isAuthorizationRequired) {
            array_push($header, 'Authorization: ' . "Bearer " . static::getToken());
        }

        return $header;
    }


    /**
     * Make CURL request for GET request.
     *
     * @param  string  $url
     * @param  array  $body
     * @param  array  $headers
     * @return string
     * @throws IdpException
     */
    public static function get($url, $body = [], $headers = null)
    {
        return static::makeMethod('get', $url, $body, $headers);
    }

    /**
     * Make CURL request for POST request.
     *
     * @param  string  $url
     * @param  array  $body
     * @param  array  $headers
     * @return string
     * @throws IdpException
     */
    public static function post($url, $body = [], $headers = null)
    {
        return static::makeMethod('post', $url, $body, $headers);
    }

    /**
     * Make CURL request for PUT request.
     *
     * @param  string  $url
     * @param  array  $body
     * @param  array  $headers
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
     * @param  string  $url
     * @param  array  $body
     * @param  array  $headers
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
     * @param  string  $method
     * @param  string  $url
     * @param  array  $body
     * @param  array  $headers
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

        $date = Carbon::parse($body ['date'], 'Asia/Dhaka')->toDateString();
        $bonus = SignInBonusLog::where('msisdn', $body['msisdn'])
                                ->where('date', $date)
                                ->where('bonus_type', $body['bonustype'])->first();
        if ($bonus) {
            $bonus->update(
                [
                    'status' => $httpCode
                ]
            );
        }

        if ($info['http_code'] == 401) {
            static::makeRequest($ch, $url, $body, $headers);
            $result = curl_exec($ch);
        }

        if ($info['http_code'] != 200) {
            throw new IdpException($result);
        }

        curl_close($ch);

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
    private static function makeRequest($ch, $url, $body, $headers)
    {
        $url = static::getHost() . $url;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }
}
