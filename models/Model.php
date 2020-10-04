<?php

use Slim\Exception\HttpNotImplementedException;

abstract class Model
{
    public $unUpdateAble = array();
    protected $columns = [];
    protected $DB = null;
    protected $tableName = null;
    public $queryStringParams = [];

    public function __construct(DBInterface $DBInterface)
    {
        $this->DB = $DBInterface;
        $this->DB->connect();
    }

    public function setQueryStringParams(array $params): void
    {
        /**
         * Set $queryStringParams to make avaliabe 
         * sorting, limits and paging of read results from database
         * 
         * @param array $params
         * @return void
         */
        foreach ($params as $key => &$value) {
            if ($key === 'ext') continue;

            preg_match('/[a-z0-9_,]*/', $value, $output_array);
            $value = $output_array[0];

            if (!in_array($key, ['limit', 'page', 'on_page', 'sort', 'sort_key']))  unset($params[$key]);
        }

        if (isset($params['limit'])     && !is_numeric($params['limit']))                           unset($params['limit']);
        if (isset($params['on_page'])   && !is_numeric($params['on_page']))                         unset($params['on_page']);
        if (isset($params['page'])      && ($params['page'] < 0 || !is_numeric($params['page'])))   unset($params['page']);
        if (isset($params['sort_key'])  && !in_array($params['sort_key'], $this->columns))          unset($params['sort_key']);
        if (isset($params['sort'])      && !in_array(strtoupper($params['sort']), ['DESC', 'ASC'])) unset($params['sort']);

        $this->queryStringParams = $params;
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

    public function exist(array $params): bool
    {
        /**
         * check is exist model with given params
         * 
         * @param array $params
         * @return bool
         */
        $sql = "SELECT id FROM $this->tableName WHERE 1=1 ";
        $queryParams = array();

        foreach ($params as $key => $value) {
            $sql .= " AND $key=:$key";
            $queryParams[":$key"] = $value;
        }

        return !empty($this->DB->query($sql, $queryParams));
    }

    public function parseData(array $data): array
    {
        throw new Exception("Mode::parseData(array \$data) need to be implemented", 501);
        return $data;
    }

    public function read(array $params = []): array
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

        extract($this->queryStringParams); //extracting variables

        if (isset($sort_key) && isset($sort))   $sql .= " ORDER BY $sort_key $sort";
        elseif (isset($sort_key))               $sql .= ' ORDER BY ' . $sort_key;
        elseif (isset($sort))                   $sql .= ' ORDER BY id ' . $sort;


        if (isset($limit))                       $sql .= ' LIMIT ' . (int)$limit;
        elseif (isset($page) && isset($on_page)) $sql .= ' LIMIT ' . (int)$page . ', ' . (int)$on_page;


        $result = $this->DB->query($sql, $queryParams);
        if (empty($result)) {
            throw new LengthException("Nothing was found in $this->tableName with parameters:" . json_encode($params), 404);
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
            throw new UnexpectedValueException("Nothing Found in $this->tableName with search params:" . json_encode($params), 404);
        }

        foreach ($result as &$r) {
            $r = $this->parseData($r);
        }
        return $result;
    }

    public function create(array $params): int
    {
        throw new Exception("Model::create() need to be implemented", 501);
        return -1;
    }

    public function update(int $id, array $params): void
    {
        if (!$this->exist(['id' => $id])) {
            throw new InvalidArgumentException("$this->tableName with id=$id do not exist. You cannot update non existing collection item.", 404);
        }

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
        if (!$this->exist(['id' => $id])) {
            throw new InvalidArgumentException("$this->tableName with id=$id do not exist. You cannot delete non existing collection item.", 404);
        }

        $this->DB->query(
            "DELETE FROM $this->tableName WHERE id=:id",
            array(':id' => $id)
        );
    }
}
