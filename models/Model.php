<?php

abstract class Model
{
    public $unUpdateAble = array();
    protected $columns = [];
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

    protected function filterVariables(array $data): array
    {
        /**
         * Unsetting unexpected variables from params
         * 
         * @param array $params
         * @return array $params filtered
         */
        foreach ($data as $key => $value) {
            if (!in_array($key, $this->columns)) {
                unset($data[$key]);
            }
        }
        return $data;
    }

    public function exist(array $params, bool $reverse = false): void
    {
        /**
         * if @param $reverse==false if item with given $params not exist, throw InvalidArgumentException
         * if @param $reverse==true if item with given $params exist, throw InvalidArgumentException
         * 
         * @param array $params
         * @param bool $reverse=false
         * 
         * @throws InvalidArgumentException
         * @return void
         */
        $sql = "SELECT id FROM $this->tableName WHERE 1=1 ";
        $queryParams = array();

        foreach ($params as $key => $value) {
            $sql .= " AND $key=:$key";
            $queryParams[":$key"] = $value;
        }

        $empty = empty($this->DB->query($sql, $queryParams));
        if ($empty) {
            $dataString = implode(',', array_keys($params));
            throw new InvalidArgumentException("$this->tableName with given $dataString do not exist. You can not perform this action.", 400);
        } elseif ($reverse && !$empty) {
            $dataString = implode(',', array_keys($params));
            throw new InvalidArgumentException("$this->tableName with given $dataString already exist. You can not perform this action.", 400);
        }
    }

    public function read(array $params = array(), string $sortKey = '', string $direction  = 'DESC'): array
    {
        /**
         * Read collection with params in param array
         * 
         * @param array $params=[] reading parameters
         * @param string $sortKey=''
         * @param string $direction='DESC'
         * 
         * @throws LengthException when nothing found
         * @return array $result
        */
        $params = $this->parseData($params);

        $sql = "SELECT * FROM $this->tableName WHERE 1=1";
        $queryParams = array();

        foreach ($params as $key => $value) {
            $sql .= " AND $key=:$key";
            $queryParams[":$key"] = $value;
        }

        if (!empty($sortKey) & !empty($direction)) {
            $sql .= " ORDER BY $sortKey $direction";
        }

        $result = $this->DB->query($sql, $queryParams);
        if (empty($result)) {
            throw new LengthException("Nothing was found in $this->tableName", 404);
        }

        foreach ($result as &$r) {
            $r = $this->parseData($r);
        }
        return $result;
    }

    // searching with LIKE %param%
    public function search(array $params, string $sortKey = 'id', string $direction  = 'DESC')
    {
        $params = $this->filterVariables($params);
        $params = $this->parseData($params);

        $sql = "SELECT * from $this->tableName WHERE 1=1";
        $queryParams = array();

        foreach ($params as $key => $value) {
            $sql .= " AND $key LIKE :$key";
            $queryParams[":$key"] = "%$value%";
        }
        $sql .= " ORDER BY $sortKey $direction";

        $result = $this->DB->query($sql, $queryParams);
        if (empty($result)) {
            throw new UnexpectedValueException($this->tableName);
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
        $this->exist(array('id' => $id));

        $params = $this->filterVariables($params);
        $params = $this->parseData($params);

        $sql = "UPDATE $this->tableName SET";
        $queryParams = array();

        foreach ($params as $key => $value) {
            if (in_array($key, $this->unUpdateAble)) {
                continue;
            }
            count($queryParams) >= 1 ? $sql .= "," : null;
            $sql .= " $key=:$key";
            $queryParams[":$key"] = $value;
        }

        $sql .= " WHERE id=:id";
        $queryParams[':id'] = $id;

        $this->DB->query($sql, $queryParams);
    }

    public function delete(int $id): void
    {
        $this->exist(array('id' => $id));

        $this->DB->query(
            "DELETE FROM $this->tableName WHERE id=:id",
            array(':id' => $id)
        );
    }
}
