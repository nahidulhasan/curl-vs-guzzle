<?php


namespace App\Services;

use App\Contracts\LoginInterface;
use App\Exceptions\IdpException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Class IdpIntegrationService
 * @package App\Services
 */
class NewIdpIntegrationService implements LoginInterface
{

    protected const IDP_TOKEN_REDIS_KEY = "IDP_TOKEN";

    /**
     * Get Host from env file
     *
     * @return string
     */
    public static function getHost()
    {
        return env('IDP_HOST');
    }

    public static function setToken()
    {
        // get token and set
        $data_param = [
            'grant_type' => 'client_credentials',
            'client_id' => env('IDP_CLIENT_ID'),
            'client_secret' => env('IDP_CLIENT_SECRET'),
            'provider' => 'users'
        ];

        $response = static::post('/oauth/token', $data_param, static::makeHeader(false));

        $response_data = json_decode($response['response']);


        if (isset($response_data->access_token)) {
            // set this token in redis
            Redis::set(self::IDP_TOKEN_REDIS_KEY, $response_data->access_token);
        }
    }

    /**
     * Get Token from env file
     *
     * @return string
     */
    public static function getToken()
    {

        if (!Redis::get(self::IDP_TOKEN_REDIS_KEY)) {
            static::setToken();
        }

        return Redis::get(self::IDP_TOKEN_REDIS_KEY);

        // return env('IDP_CLIENT_TOKEN');
    }


    /**
     * Send request for user registration
     *
     * @param $data
     * @return string
     */
    public static function registrationRequest($data)
    {
        return static::post('/api/customers', $data);
    }


    /**
     * Send request for user login
     *
     * @param $data
     * @return string
     */
    public static function loginRequest($data)
    {
        return static::post('/oauth/token', $data);
    }


    /**
     * Send request for user login
     *
     * @param $data
     * @return string
     */
    public function login($data)
    {
        return static::post('/oauth/token', $data);
    }

    /**
     * Send Request for token validation
     *
     * @param $token
     * @return string
     */
    public static function tokenValidationRequest($token)
    {
        return static::post('/api/check/user/token', $token);
    }


    /**
     * Send request for customer info
     *
     * @param $msisdn
     * @return mixed
     */
    public static function getCustomerInfo($msisdn)
    {
        return static::get('/api/customers/' . $msisdn);
    }


    /**
     * Send request for customer Basic info
     *
     * @param $msisdn
     * @return mixed
     */
    public static function getCustomerBasicInfo($msisdn)
    {
        return static::get('/api/v1/customers/basic-info/' . $msisdn);
    }


    /**
     * Send request for customer Profile Image
     *
     * @param $msisdn
     * @return mixed
     */
    public static function getCustomerProfileImage($msisdn)
    {
        return static::get('/api/v1/customers/profile-image/' . $msisdn);
    }


    /**
     * Send request for user login
     *
     * @param $data
     * @return string
     */
    public static function otpGrantTokenRequest($data)
    {
        return static::post('/oauth/token', $data);
    }


    /**
     * Send request for user login
     *
     * @param $data
     * @return string
     */
    public static function otpRefreshTokenRequest($data)
    {
        return static::post('/oauth/token', $data);
    }

    public static function setPassword($data, Request $request)
    {
        return static::post('/api/v1/customers/set/password', $data, [
            'Authorization' => "Bearer " . $request->bearerToken()
        ]);
    }

    /**
     * Send Request for update password
     *
     * @param $data
     * @return string
     */
    public static function forgetPasswordRequest($data)
    {
        return static::put('/api/customers/forget/password', $data);
    }


    /**
     * Send Request for update password
     *
     * @param $data
     * @return string
     */
    public static function changePasswordRequest($data)
    {
        return static::put('/api/customers/change/password', $data);
    }


    public static function updateCustomer($data, Request $request)
    {
        $header = array(
            'Accept: application/json',
            'Content-Type: application/json',
            'Expect: 100-continue',
            'Authorization:' . 'Bearer ' . $request->bearerToken()
        );

        return static::post('/api/v1/customers/update/perform', $data, $header);
    }


    /**
     * Make the header array with authentication.
     *
     * @param bool $isAuthorizationRequired
     * @return array
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
     * @param string $url
     * @param array $body
     * @param array $headers
     * @return string
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
     * @return string
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
     * @return string
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
     * @return string
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
            self::setToken();
            static::makeRequest($ch, $url, $body, $headers);
            $result = curl_exec($ch);
        }

        if ($info['http_code'] >= 500) {
            try {
                Log::channel('idpServiceLog')->info([
                    'url' => $info['url'],
                    'response' => $result
                ]);
            } catch (Exception $e) {
            }

            throw new IdpException($result);
        }

        curl_close($ch);
        // return $result;

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

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }
}
