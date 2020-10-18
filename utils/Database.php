<?php
namespace utils;
use utils\DBInterface;

class Database implements DBInterface
{
    private $conn = null;
    public function __construct(
        string $user,
        string $password,
        string $host,
        string $name,
        string $charset
    ) {
        $this->user = $user;
        $this->password = $password;
        $this->host = $host;
        $this->name = $name;
        $this->charset = $charset;
    }
    public function connect(): void
    {
        try {
            $this->conn = new \PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->name . ';charset=' . $this->charset,
                $this->user,
                $this->password
            );
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            throw new HttpServiceNotAvaliableException("Database connection error: " . $e->getMessage());
        }
    }

    private function handleException(\PDOException $e): void
    {
        switch ($e->getCode()) {
            case 23000:
                throw new \Exception("Database Integrity: " . substr($e->getMessage(), 17), 400);
                break;
            case 42000:
                throw new \Exception("SQL Syntax error:" . substr($e->getMessage(), 17), 500);
                break;
            default:
                throw new \Exception("Database Interface Exception: " . substr($e->getMessage(), 17), 500);
                break;
        }
    }

    public function query(string $sql, array $params = []): array
    {
        $results = array();
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
        } catch (\PDOException $e) {
            $this->handleException($e);
        }

        $keyWord = strtoupper(explode(' ', $sql)[0]);
        if ($keyWord === "SELECT") {
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            $results = array();
        }

        return $results;
    }

    public function lastInsertID(): int
    {
        try {
            $stmt = $this->conn->prepare("SELECT LAST_INSERT_ID() AS 'id'");
            $stmt->execute();
        } catch (\PDOException $e) {
            $this->handleException($e);
        }
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return (int) $results[0]['id'];
    }
}

class HttpServiceNotAvaliableException extends \Exception
{
    public function __construct(string $message, int $code = 503)
    {
        parent::__construct($message, $code);
        $this->code = $code;
    }
}