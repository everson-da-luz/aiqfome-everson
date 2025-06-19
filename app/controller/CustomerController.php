<?php

namespace Controller;

require_once 'controller/ApiController.php';
require_once 'model/Customer.php';

use Controller\ApiController;
use Controller\ErrorController;
use Model\Customer;

class CustomerController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        
        if (! $this->checkTokenApi()) {
            throw new \Exception('Não autorizado.', 401);
        }
    }
    
    public function get($id)
    {
        try {
            if (! $this->isMethod('GET')) {
                throw new \Exception('Não autorizado.', 401);
            }
            
            $modelCustomer = new Customer();
            $customer = $modelCustomer->getById($id);

            if (empty($customer)) {
                throw new \Exception('Cliente não encontrado.', 400);
            }

            $response = $this->response('', 200, $customer);
        } catch (\Exception $e) {
            $errorController = new ErrorController($e);
            $response = $errorController->show();
        }

        return $response;
    }

    public function create()
    {
        try {
            if (! $this->isMethod('POST')) {
                throw new \Exception('Não autorizado.', 401);
            }
            
            $modelCustomer = new Customer();
            $modelCustomer->validateDataToCreate();
            $data = $modelCustomer->getData();

            if ($modelCustomer->create($data)) {
                $data['id'] = $modelCustomer::getInstance()->lastInsertId();
                $response = $this->response('Cliente criado com sucesso.', 200, $data);
            } else {
                throw new \Exception('Houve um erro ao tentar criar o cliente.', 500);
            }
        } catch (\Exception $e) {
            $errorController = new ErrorController($e);
            $response = $errorController->show();
        }

        return $response;
    }

    public function update($id)
    {
        try {
            if (! $this->isMethod('PUT')) {
                throw new \Exception('Não autorizado.', 401);
            }
            
            $modelCustomer = new Customer();
            $modelCustomer->validateDataToUpdate($id);
            $data = $modelCustomer->getData();

            if ($modelCustomer->update($data)) {
                $response = $this->response('Cliente atualizado com sucesso.', 200, $data);
            } else {
                throw new \Exception('Houve um erro ao tentar atualizar o cliente.', 500);
            }
        } catch (\Exception $e) {
            $errorController = new ErrorController($e);
            $response = $errorController->show();
        }

        return $response;
    }

    public function delete($id)
    {
        try {
            if (! $this->isMethod('DELETE')) {
                throw new \Exception('Não autorizado.', 401);
            }
            
            $modelCustomer = new Customer();

            if (! $modelCustomer->customerExists($id)) {
                throw new \Exception('Cliente não encontrado.', 400);
            }

            if ($modelCustomer->delete($id)) {
                $response = $this->response('Cliente removido com sucesso.', 200, []);
            } else {
                throw new \Exception('Houve um erro ao tentar remover o cliente.', 500);
            }
        } catch (\Exception $e) {
            $errorController = new ErrorController($e);
            $response = $errorController->show();
        }

        return $response;
    }
}
