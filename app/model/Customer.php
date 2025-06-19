<?php

namespace Model;

require_once 'lib/DatabaseConnection.php';

use Lib\DatabaseConnection;

class Customer extends DatabaseConnection
{
    private $table = 'customer';
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

    public function emailExists($email)
    {
        $sql = "SELECT COUNT(id) FROM $this->table WHERE email = :email";
        $pdo = DatabaseConnection::getInstance()->prepare($sql);
        $pdo->bindParam(':email', $email);
        $pdo->execute();
        
        return $pdo->fetchColumn() > 0;
    }

    public function customerExists($id)
    {
        $sql = "SELECT COUNT(id) FROM $this->table WHERE id = :id";
        $pdo = DatabaseConnection::getInstance()->prepare($sql);
        $pdo->bindParam(':id', $id);
        $pdo->execute();
        
        return $pdo->fetchColumn() > 0;
    }

    public function validateDataToCreate()
    {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

        if (! $name) {
            throw new \Exception('Nome obrigatório.', 400);
        }

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('E-mail obrigatório ou inválido.', 400);
        }
        
        if ($this->emailExists($email)) {
            throw new \Exception('E-mail já cadastrado.', 400);
        }

        $this->data = [
            'name' => $name,
            'email' => $email,
            'created_at' => date('Y-m-d H:i:s')
        ];
    }

    public function validateDataToUpdate($id)
    {
        $dataInDb = $this->getById($id);

        $json = file_get_contents('php://input');
        $putData = json_decode($json, true);
    
        $name = isset($putData['name']) ? filter_var($putData['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;
        $email = isset($putData['email']) ? filter_var($putData['email'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;

        if (! $this->customerExists($id)) {
            throw new \Exception('Cliente não encontrado.', 400);
        }

        if (isset($name) && !$name) {
            throw new \Exception('Nome obrigatório.', 400);
        }

        if (isset($email) && !$email) {
            throw new \Exception('E-mail obrigatório ou inválido.', 400);
        }
        
        if (isset($email) && $this->emailExists($email)) {
            throw new \Exception('E-mail já cadastrado.', 400);
        }

        $this->data = [
            'id' => $id,
            'name' => $name ?? $dataInDb['name'],
            'email' => $email ?? $dataInDb['email'],
            'created_at' => $dataInDb['created_at'],
            'updated_at' => date('Y-m-d H:i:s')
        ];
    }

    public function create($data)
    {
        $sql = "INSERT INTO $this->table (name, email, created_at) VALUES (:nome, :email, :created_at)";

        $pdo = DatabaseConnection::getInstance()->prepare($sql);
        $pdo->bindParam(':nome', $data['name']);
        $pdo->bindParam(':email', $data['email']);
        $pdo->bindParam(':created_at', $data['created_at']);

        if ($pdo->execute()) {
            return true;
        }

        return false;
    }

    public function update($data)
    {
        $sql = "UPDATE $this->table SET name = :name, email = :email, updated_at = :updated_at WHERE id = :id";

        $pdo = DatabaseConnection::getInstance()->prepare($sql);
        $pdo->bindParam(':name', $data['name']);
        $pdo->bindParam(':email', $data['email']);
        $pdo->bindParam(':updated_at', $data['updated_at']);
        $pdo->bindParam(':id', $data['id']);

        if ($pdo->execute()) {
            return true;
        }

        return false;
    }

    public function delete($id)
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
