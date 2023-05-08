<?php

namespace app\Utils;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;
use Config;

class Proxy 
{
    private $base_uri;
    private $plan;
    private $users;
    
    public function __construct()
    {
        $this->base_uri = Config::get('proxy_uri.base_uri');
        $this->plan = Config::get('proxy_uri.plan');
        $this->users = Config::get('proxy_uri.users');
    }

    public function validateEnrollee($code)
    {
        $url = $this->base_uri . $this->users;
        $http_verb = 'GET';
        $json_object = ['enrolleeId' => $code];
        return self::proxyConnector($url, $http_verb, $json_object);
    }

    public function getPlanBenefits($plan, $client_code)
    {
        $url = $this->base_uri . $this->plan . $plan . '/' . $client_code;
        $http_verb = 'GET';
        $json_object = [];
        return self::proxyConnector($url, $http_verb, $json_object);
    }

    public function getDependents($principal_code)
    {
        $url = $this->base_uri . $this->users . 'dependents';
        $http_verb = 'GET';
        $json_object = ['principalCode' => $principal_code];
        return self::proxyConnector($url, $http_verb, $json_object);
    }

    protected static function proxyConnector($url, $http_verb, $json_object)
    {
        try {
            $client = new Client();
            $response = $client->request($http_verb, $url, [
                'json' => $json_object,
                'headers' => [
                'Accept' => 'application/json',
                'auth_password' => Config::get('keys.auth_password'),
                'Content-Type' => 'application/json',
                ], 
            ]);
            return $response;
        } catch (BadResponseException $e) {
            return $e->getResponse();
        } catch (ConnectException $e) {
            //return guzzlehttp response error message if the connection fails
            return $e->getResponse();
        }
    }
}