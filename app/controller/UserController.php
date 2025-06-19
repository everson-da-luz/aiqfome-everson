<?php

namespace Controller;

require_once 'model/User.php';

use Controller\ErrorController;
use Model\User;

class UserController extends ApiController
{
    public function login()
    {
        try {
            if (! $this->isMethod('POST')) {
                throw new \Exception('Não autorizado.', 401);
            }

            $modelUser = new User();
            $modelUser->validateDataToLogin();
            $data = $modelUser->getData();

            if ($modelUser->saveToken($data)) {
                $response = $this->response('Login efetuado com sucesso.', 200, $data);
            } else {
                throw new \Exception('Houve um erro ao tentar logar.', 500);
            }
        } catch (\Exception $e) {
            $errorController = new ErrorController($e);
            $response = $errorController->show();
        }

        return $response;
    }

    public function logout()
    {
        try {
            if (!$this->checkTokenApi() || !$this->isMethod('POST')) {
                throw new \Exception('Não autorizado.', 401);
            }

            $modelUser = new User();

            if ($modelUser->logout($this->getTokenApi())) {
                $response = $this->response('Logout efetuado com sucesso.', 200, []);
            } else {
                throw new \Exception('Houve um erro ao tentar deslogar.', 500);
            }
        } catch (\Exception $e) {
            $errorController = new ErrorController($e);
            $response = $errorController->show();
        }

        return $response;
    }
}
