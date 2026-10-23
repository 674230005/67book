<?php

/**
 * Database.php
 * คลาสเชื่อมต่อฐานข้อมูล MySQL ด้วย PDO
 * ใช้งานร่วมกันทั้งทีมในโปรเจกต์ 67BookStore
 */

class Database
{
    // ข้อมูลการเชื่อมต่อฐานข้อมูล
    private string $host = '127.0.0.1';
    private string $dbName = 'bookstore_novel_manga';
    private string $username = 'root';
    private string $password = '';

    // เก็บการเชื่อมต่อ PDO
    private ?PDO $pdo = null;

    /**
     * เชื่อมต่อฐานข้อมูล MySQL
     * สร้างการเชื่อมต่อเพียงครั้งเดียวต่อออบเจ็กต์
     */
    public function connect(): PDO
    {
        if ($this->pdo === null) {
            $dsn = "mysql:host={$this->host};"
                 . "dbname={$this->dbName};"
                 . "charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $this->pdo = new PDO(
                $dsn,
                $this->username,
                $this->password,
                $options
            );
        }

        return $this->pdo;
    }
}

