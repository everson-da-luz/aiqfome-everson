<?php

namespace Controller;

require_once 'controller/ApiController.php';
require_once 'model/Favorite.php';

use Controller\ApiController;
use Controller\ErrorController;
use Model\Favorite;

class FavoriteController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        
        if (! $this->checkTokenApi()) {
            throw new \Exception('Não autorizado.', 401);
        }
    }
    
    public function get($customerId)
    {
        try {
            if (! $this->isMethod('GET')) {
                throw new \Exception('Não autorizado.', 401);
            }

            $modelFavorite = new Favorite();
            $favorite = $modelFavorite->getByCustomerId($customerId);

            if (empty($favorite)) {
                throw new \Exception('Não existe favoritos para esse cliente.', 400);
            }

            $response = $this->response('', 200, $favorite);
        } catch (\Exception $e) {
            $errorController = new ErrorController($e);
            $response = $errorController->show();
        }

        return $response;
    }

    public function add()
    {
        try {
            if (! $this->isMethod('POST')) {
                throw new \Exception('Não autorizado.', 401);
            }

            $modelFavorite = new Favorite();
            $modelFavorite->validateDataToAdd();
            $data = $modelFavorite->getData();

            if ($modelFavorite->add($data)) {
                $data['id'] = $modelFavorite::getInstance()->lastInsertId();
                $response = $this->response('Produto adicionado aos favoritos com sucesso.', 200, $data);
            } else {
                throw new \Exception('Houve um erro ao tentar adicionar o produto aos favoritos.', 500);
            }
        } catch (\Exception $e) {
            $errorController = new ErrorController($e);
            $response = $errorController->show();
        }

        return $response;
    }

    public function remove($id)
    {
        try {
            if (! $this->isMethod('DELETE')) {
                throw new \Exception('Não autorizado.', 401);
            }

            $modelfavorite = new favorite();

            if (! $modelfavorite->favoriteExists($id)) {
                throw new \Exception('Favorito não encontrado.', 400);
            }

            if ($modelfavorite->remove($id)) {
                $response = $this->response('Favorito removido com sucesso.', 200, []);
            } else {
                throw new \Exception('Houve um erro ao tentar remover o favorito.', 500);
            }
        } catch (\Exception $e) {
            $errorController = new ErrorController($e);
            $response = $errorController->show();
        }

        return $response;
    }
}
