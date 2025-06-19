<?php

namespace Lib;

abstract class Api
{
    private $apiUrl;
    private $apiKey;
    private $cURL;
    private $params = [];

    public function __construct($apiKey = null)
    {
        if (! empty($apiKey)) {
            $this->setApiKey($apiKey);
        }
    }

    public function __destruct()
    {
        if (is_resource($this->cURL)) {
            $this->closeCUrl();
        }
    }

    public function setApiUrl($apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function setParams(array $params)
    {
        $this->params = $params;
    }

    public function addParams($key = null, $value = null)
    {
        if (empty($key)) {
            $this->params[] = $value;
        } else {
            $this->params[$key] = $value;
        }
    }

    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function getParams()
    {
        return $this->params;
    }

    private function initalizeCUrl()
    {
        $this->cURL = curl_init();
    }

    private function closeCUrl()
    {
        curl_close($this->cURL);
    }

    private function cUrlSetOpt($requestUrl)
    {
        curl_setopt($this->cURL, CURLOPT_URL, $requestUrl);
        curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, true);

        if (! empty($this->apiKey)) {
            curl_setopt($this->cURL, CURLOPT_HTTPHEADER, [
                'Authorization: Basic ' . $this->apiKey
            ]);
        }
    }

    private function executeCUrl()
    {
        return curl_exec($this->cURL);
    }

    protected function checkCUrlError()
    {
        return curl_errno($this->cURL);
    }

    public function execute()
    {
        $this->initalizeCUrl();

        $apiUrl = $this->getApiUrl();

        if (!empty($this->params)) {
            $queryString = http_build_query($this->getParams());
            $apiUrl = $apiUrl . '?' . $queryString;
        }

        $this->cUrlSetOpt($apiUrl);

        $response = $this->executeCUrl();

        $this->closeCUrl();

        return $response;
    }
}
