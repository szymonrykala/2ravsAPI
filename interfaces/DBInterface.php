<?php

interface DBInterface
{
    public function connect(): void;
    public function query(string $sql, array $params = []): array;
}

class Database implements DBInterface
{
    private $conn = null;
    function __construct()
    {
    }

    public function connect(): void
    {
        try {
            $this->conn = new PDO(DSN, DB_USER, DB_PASS);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception("DBInterface connect error: ", 500);
        }
    }

    public function query(string $sql, array $params = []): array
    {
        $results = array();
        // print_r($sql);print_r($params);
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
        } catch (PDOException $e) {
            // throw new Exception("DBInterface query error: " . $e->getMessage(), 500, $e);
            switch ($e->getCode()) {
                case 23000:
                    throw new APIException("Database Integrity: " . substr($e->getMessage(), 17), 400);
                    break;
                case 42000:
                    throw new APIException("SQL Syntax error:" . substr($e->getMessage(), 17), 500);
                    break;
            }
            echo $e->getCode();
            echo $e->getMessage();
            exit();
        }

        $keyWord = strtoupper(explode(' ', $sql)[0]);
        if ($keyWord === "SELECT") {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $results = array();
        }

        return $results;
    }

    public function lastInsertID()
    {
        try {
            $stmt = $this->conn->prepare("SELECT LAST_INSERT_ID() AS 'id'");
            $stmt->execute();
        } catch (Exception $e) {
            echo $e->getMessage();
            exit();
        }
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return (int) $results[0]['id'];
    }
}
