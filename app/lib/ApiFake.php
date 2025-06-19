<?php

namespace Lib;

require_once 'lib/Api.php';

use Lib\Api;

class ApiFake extends Api
{
    private $urlApiFake = 'https://fakestoreapi.com';

    public function getAllProducts()
    {
        $this->setApiUrl($this->urlApiFake . '/products');
        $response = $this->execute();

        if ($this->checkCUrlError()) {
            throw new \Exception('Houve um erro ao tentar acessar a API de produtos', 500);
        }

        return json_decode($response, true);
    }

    public function getProduct($id)
    {
        $this->setApiUrl($this->urlApiFake . '/products/' . $id);
        $response = $this->execute();

        if ($this->checkCUrlError()) {
            throw new \Exception('Houve um erro ao tentar acessar a API de produtos', 500);
        }

        return json_decode($response, true);
    }
}
