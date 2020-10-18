<?php
namespace utils;

interface DBInterface
{
    public function connect(): void;
    public function query(string $sql, array $params = []): array;
    public function lastInsertID(): int;
}