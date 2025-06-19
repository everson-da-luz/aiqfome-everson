<?php

namespace Lib;

class DatabaseConnection
{
    private static $instance;

    public function __construct()
    {
        $config = require 'config/database.php';
        $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";

        try {
            self::$instance = new \PDO($dsn, $config['username'], $config['password']);
        } catch (\PDOException $e) {
            throw new \Exception('Houve um erro ao conectar com o banco de dados.', 500);
        }
    }

    public static function getInstance() 
    {
        return self::$instance;
    }
}
