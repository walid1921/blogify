<?php

    class Database {

        private $host = "localhost";
        private $user = "root";
        private $password = "root";
        private $dbname = "registration_test";
        private $pdo;

        public function __construct() {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";

            try {
                $this-> pdo = new PDO($dsn, $this->user, $this->password);
                $this-> pdo-> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // echo "Connected successfully!";

            } catch (PDOException $e) {
                die("Connection failed :" . $e->getMessage());
            }
        }

        public function getConnection() {
            return $this->pdo;
        }

    }

// to use this you just have to include this file and create an instance of the Database class like this:
// $db = new Database();
// $pdo = $db->getConnection();
// and then you can use the $pdo variable to interact with the database.
