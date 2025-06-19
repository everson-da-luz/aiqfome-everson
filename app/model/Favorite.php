<?php

namespace Model;

require_once 'lib/DatabaseConnection.php';
require_once 'lib/ApiFake.php';
require_once 'model/Customer.php';

use Lib\ApiFake;
use Lib\DatabaseConnection;
use Model\Customer;

class Favorite extends DatabaseConnection
{
    private $table = 'favorite';
    private $data = [];

    public function getData()
    {
        return $this->data;
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM $this->table WHERE id = :id";
        $pdo = DatabaseConnection::getInstance()->prepare($sql);
        $pdo->bindParam(':id', $id);
        $pdo->execute();

        $result = $pdo->fetch(\PDO::FETCH_ASSOC);

        return $result ?: [];
    }

    public function getByCustomerId($customerId)
    {
        $sql = "SELECT * FROM $this->table WHERE customer_id = :customer_id";
        $pdo = DatabaseConnection::getInstance()->prepare($sql);
        $pdo->bindParam(':customer_id', $customerId);
        $pdo->execute();

        $result = $pdo->fetchAll(\PDO::FETCH_ASSOC);

        return $result ?: [];
    }

    public function favoriteExists($id)
    {
        $sql = "SELECT COUNT(id) FROM $this->table WHERE id = :id";
        $pdo = DatabaseConnection::getInstance()->prepare($sql);
        $pdo->bindParam(':id', $id);
        $pdo->execute();
        
        return $pdo->fetchColumn() > 0;
    }

    public function existsInFavorite($customerId, $productId)
    {
        $sql = "SELECT COUNT(id) FROM $this->table WHERE customer_id = :customer_id AND product_id = :product_id";
        $pdo = DatabaseConnection::getInstance()->prepare($sql);
        $pdo->bindParam(':customer_id', $customerId);
        $pdo->bindParam(':product_id', $productId);
        $pdo->execute();
        
        return $pdo->fetchColumn() > 0;
    }

    public function validateDataToAdd()
    {
        $customerId = filter_input(INPUT_POST, 'customer_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $productId = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT);

        $apiFake = new ApiFake();
        $productFake = $apiFake->getProduct($productId);

        if (! $customerId) {
            throw new \Exception('Cliente obrigatório.', 400);
        }

        $modelCustomer = new Customer();

        if (! $modelCustomer->customerExists($customerId)) {
            throw new \Exception('Cliente não encontrado.', 400);
        }

        if (! $productId) {
            throw new \Exception('Produto obrigatório.', 400);
        }
        
        if ($this->existsInFavorite($customerId, $productId)) {
            throw new \Exception('Produto já adicionado aos favoritos.', 400);
        }

        if (empty($productFake)) {
            throw new \Exception('Produto não encontrado.', 400);
        }

        $this->data = [
            'customer_id' => $customerId,
            'product_id' => $productId,
            'title' => $productFake['title'] ?? null,
            'image' => $productFake['image'] ?? null,
            'price' => $productFake['price'] ?? null,
            'review' => $productFake['review'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
            'product' => $productFake
        ];
    }

    public function add($data)
    {
        $sql = "INSERT INTO $this->table (customer_id, product_id, title, image, price, review, created_at) 
            VALUES (:customer_id, :product_id, :title, :image, :price, :review, :created_at)";

        $pdo = DatabaseConnection::getInstance()->prepare($sql);
        $pdo->bindParam(':customer_id', $data['customer_id']);
        $pdo->bindParam(':product_id', $data['product_id']);
        $pdo->bindParam(':title', $data['title']);
        $pdo->bindParam(':image', $data['image']);
        $pdo->bindParam(':price', $data['price']);
        $pdo->bindParam(':review', $data['review']);
        $pdo->bindParam(':created_at', $data['created_at']);

        if ($pdo->execute()) {
            return true;
        }

        return false;
    }

    public function remove($id)
    {
        $sql = "DELETE FROM $this->table WHERE id = :id";

        $pdo = DatabaseConnection::getInstance()->prepare($sql);
        $pdo->bindParam(':id', $id);

        if ($pdo->execute()) {
            return true;
        }

        return false;
    }
}
