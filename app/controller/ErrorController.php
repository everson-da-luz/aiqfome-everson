<?php

namespace Controller;

require_once 'controller/ApiController.php';

use Controller\ApiController;

class ErrorController extends ApiController
{
    private $exception;

    public function __construct(\Exception $e)
    {
        $this->exception = $e;
    }

    public function show()
    {
        return $this->response($this->exception->getMessage(), $this->exception->getCode(), []);
    }
}
