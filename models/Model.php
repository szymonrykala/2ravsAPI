<?php

abstract class Model
{
    public $unUpdateAble = array();
    public $columns = [];
    protected $DB = null;
    protected $tableName = null;

    public function __construct(DBInterface $DBInterface)
    {
        $this->DB = $DBInterface;
        $this->DB->connect();
    }

    public function parseData(array $data): array
    {
        throw new Exception("Mode::parseData(array \$data) need to be implemented");
        return $data;
    }


    public function exist(array $params): bool
    {
        $params = $this->parseData($params);
        $sql = "SELECT id FROM $this->tableName WHERE 1=1 ";
        $queryParams = array();

        foreach ($params as $key => $value) {
            $sql .= " AND $key=:$key";
            $queryParams[":$key"] = $value;
        }

        $exist = (bool) !empty($this->DB->query($sql, $queryParams));
        return $exist;
    }

    public function read(array $params = array(), string $sortKey = 'id', string $direction  = 'DESC'): array
    {
        /* 
        maybe make filtering by columns in array like in search?
         */
        $params = $this->parseData($params);

        $sql = "SELECT * FROM $this->tableName WHERE 1=1";
        $queryParams = array();

        foreach ($params as $key => $value) {
            $sql .= " AND $key=:$key";
            $queryParams[":$key"] = $value;
        }
        $sql .= " ORDER BY $sortKey $direction";

        $result = $this->DB->query($sql, $queryParams);
        if (empty($result)) {
            throw new NothingFoundException($this->tableName);
        }

        foreach ($result as &$r) {
            $r = $this->parseData($r);
        }
        return $result;
    }

    // searching with LIKE %param%
    public function search(array $params, string $sortKey = 'id', string $direction  = 'DESC')
    {
        $params = $this->parseData($params);

        $sql = "SELECT * from $this->tableName WHERE 1=1";
        $queryParams = array();

        foreach ($params as $key => $value) {
            if (!in_array($key, $this->columns)) {
                continue;
            }
            $sql .= " AND $key LIKE :$key";
            $queryParams[":$key"] = "%$value%";
        }
        $sql .= " ORDER BY $sortKey $direction";

        $result = $this->DB->query($sql, $queryParams);
        if (empty($result)) {
            throw new NothingFoundException($this->tableName);
        }

        foreach ($result as &$r) {
            $r = $this->parseData($r);
        }
        return $result;
    }

    public function create(array $params): int
    {
        return -1;
    }

    public function update(int $id, array $params): void
    {
        $sql = "UPDATE $this->tableName SET";
        $queryParams = array();

        $params = $this->parseData($params);

        foreach ($params as $key => $value) {
            if (in_array($key, $this->unUpdateAble)) {
                throw new UnUpdateableParameterException($key);
            } else {

                if (empty($value) && $value !== 0 && $value !== false) {
                    throw new EmptyVariableException($key);
                }
                count($queryParams) >= 1 ? $sql .= "," : null;
                $sql .= " $key=:$key";
                $queryParams[":$key"] = $value;
            }
        }

        $sql .= " WHERE id=:id";
        $queryParams[':id'] = $id;

        $this->DB->query($sql, $queryParams);
    }

    public function delete(int $id): void
    {
        if (!$this->exist(array('id' => $id))) {
            throw new NothingFoundException($this->tableName);
        }
        $this->DB->query(
            "DELETE FROM $this->tableName WHERE id=:id",
            array(':id' => $id)
        );
    }
}
