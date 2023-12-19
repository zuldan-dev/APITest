<?php

namespace App\Models;

use PDO,

PDOException;

abstract class Model
{
    protected PDO $pdo;

    public function connect(): void
    {
        try {
            $this->pdo = new PDO(
                'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'],
                $_ENV['DB_USER'],
                $_ENV['DB_PASSWORD']
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('MySQL connection failed: ' . $e->getMessage());
        }
    }
}
