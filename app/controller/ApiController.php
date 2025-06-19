<?php

namespace Controller;

require_once 'model/User.php';

use Model\User;

abstract class ApiController
{
    protected $method;
    private $tokenApi;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];

        $this->setTokenApi();
    }

    protected function isMethod($method)
    {
        return $this->method == $method;
    }

    protected function getTokenApi()
    {
        return $this->tokenApi;
    }

    private function setTokenApi()
    {
        $tokenApi = null;
        $headers = null;

        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
        } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
        } elseif (function_exists('getallheaders')) {
            $requestHeaders = getallheaders();

            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }

        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                $tokenApi = $matches[1];
            }
        }

        $this->tokenApi = $tokenApi;
    }

    protected function checkTokenApi()
    {
        $modelUser = new User();

        if (!empty($this->tokenApi) && $modelUser->tokenApiExists($this->tokenApi)) {
            return true;
        }

        return false;
    }

    protected function response($message = null, $code = null, $data = [])
    {
        return [
            'message' => $message,
            'code' => $code,
            'data' => $data
        ];
    }
}
