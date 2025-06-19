<?php

namespace Model;

require_once 'lib/DatabaseConnection.php';

use Lib\DatabaseConnection;

class User extends DatabaseConnection
{
    private $table = 'user';
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

    public function getByUserNameAndPassword($username, $password)
    {
        $sql = "SELECT * FROM $this->table WHERE username = :username AND password = :password";
        $pdo = DatabaseConnection::getInstance()->prepare($sql);
        $pdo->bindParam(':username', $username);
        $pdo->bindParam(':password', $password);
        $pdo->execute();

        $result = $pdo->fetch(\PDO::FETCH_ASSOC);

        return $result ?: [];
    }

    public function tokenApiExists($tokenApi)
    {
        $sql = "SELECT COUNT(id) FROM $this->table WHERE token_api = :token_api";
        $pdo = DatabaseConnection::getInstance()->prepare($sql);
        $pdo->bindParam(':token_api', $tokenApi);
        $pdo->execute();
        
        return $pdo->fetchColumn() > 0;
    }

    public function validateDataToLogin()
    {
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (! $username) {
            throw new \Exception('Usuário obrigatório.', 400);
        }

        if (! $password) {
            throw new \Exception('Password obrigatório.', 400);
        }

        $user = $this->getByUserNameAndPassword($username, md5($password));

        if (empty($user)) {
            throw new \Exception('Usuário ou senha inválida.', 400);
        }

        $this->data = [
            'id' => $user['id'],
            'username' => $user['username'],
            'token_api' => $this->crateToken($user['username'], $user['password'])
        ];
    }

    private function crateToken($username, $password) 
    {
        // Token simples, apenas para fins didáticos
        return base64_encode(md5($username) . md5($password));
    }

    public function saveToken($data)
    {
        $sql = "UPDATE $this->table SET token_api = :token_api WHERE id = :id";

        $pdo = DatabaseConnection::getInstance()->prepare($sql);
        $pdo->bindParam(':token_api', $data['token_api']);
        $pdo->bindParam(':id', $data['id']);

        if ($pdo->execute()) {
            return true;
        }

        return false;
    }

    public function logout($tokenApi)
    {
        $sql = "UPDATE $this->table SET token_api = NULL WHERE token_api = :token_api";

        $pdo = DatabaseConnection::getInstance()->prepare($sql);
        $pdo->bindParam(':token_api', $tokenApi);

        if ($pdo->execute()) {
            return true;
        }

        return false;
    }
}
