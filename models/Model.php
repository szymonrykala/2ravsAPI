<?php
namespace models;
use utils\DBInterface;

abstract class Model
{
    protected array $columns;
    protected DBInterface $DB;
    protected string $tableName;
    protected array $queryStringParams = [];
    protected array $searchParams;
    protected string $searchMode = "=";

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
        // checking right format of variables
        if (isset($params['limit'])     && !is_numeric($params['limit']))                           unset($params['limit']);
        if (isset($params['on_page'])   && !is_numeric($params['on_page']))                         unset($params['on_page']);
        if (isset($params['page'])      && ($params['page'] < 0 || !is_numeric($params['page'])))   unset($params['page']);
        if (isset($params['sort_key'])  && !in_array($params['sort_key'], $this->columns))          unset($params['sort_key']);
        if (isset($params['sort'])      && !in_array(strtoupper($params['sort']), ['DESC', 'ASC'])) unset($params['sort']);

        $this->queryStringParams = $params;
    }

    public function setSearch(string $mode = "=", array $params = []): void
    {
        $regex = strtoupper($mode) === 'REGEXP' ? '/[\w\s%-:\.\*\|\{\}\[\]\(\)\?\+\\\,]*/' : '/[\w\s:%-]*/';

        foreach ($params as $key => &$value) {
            if (!in_array($key, $this->columns)) unset($params[$key]);

            preg_match($regex, $value, $output_array);
            $value = $output_array[0];
        }
        $this->searchMode = $mode;
        $this->searchParams = $params;
    }

    private function buildDataString(array $params, string $connector = null): array
    {
        /**
         * Building data array and SQL string to PDO
         * 
         * @param array $params - array data
         * @param string $connector=null - LIKE, =, <, >, REGEXP 
         */
        if (!isset($connector)) $connector = $this->searchMode; // by default: '='
        $queryParams = [];
        $sql = "";
        foreach ($params as $key => $value) {
            $sql .= " AND `$this->tableName`.`$key` $connector :$key";
            $queryParams[":$key"] = $value;
        }
        return ['sql' => $sql, 'params' => $queryParams];
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
        ['sql' => $sqlData, 'params' => $queryParams] = $this->buildDataString($params, "=");
        $sql .= $sqlData;

        return !empty($this->DB->query($sql, $queryParams));
    }

    public function parseData(array &$data): void
    {
        throw new \Exception("Mode::parseData(array \$data) need to be implemented", 501);
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
         * @throws HttpNotFoundException when nothing found
         * @return array $result
         */
        foreach ($params as $key => $value) {
            if (!in_array($key, $this->columns)) {
                unset($params[$key]);
            }
        }
        $this->parseData($params);

        // =========MENAGE SEARCHING=========
        $searchSQL = '';
        $searchParams = [];
        if (!empty($this->searchParams)) {
            ['sql' => $searchSQL, 'params' => $searchParams] = $this->buildDataString($this->searchParams);
        }
        // ======== NORMAL READING ===========
        $sql = "SELECT * FROM `$this->tableName` WHERE 1=1";
        ['sql' => $sqlData, 'params' => $queryParams] = $this->buildDataString($params, '=');

        $sql .= $sqlData .= $searchSQL;
        $queryParams = array_merge($searchParams, $queryParams);

        // =======PARSING SORTING, PAGING AND LIMIT=======
        extract($this->queryStringParams); //extracting variables

        if (isset($sort_key, $sort))   $sql .= " ORDER BY $sort_key $sort";
        elseif (isset($sort_key))               $sql .= ' ORDER BY ' . $sort_key;
        elseif (isset($sort))                   $sql .= ' ORDER BY id ' . $sort;


        if (isset($limit))                       $sql .= ' LIMIT ' . (int)$limit;
        elseif (isset($page, $on_page)) $sql .= ' LIMIT ' . (int)$page . ', ' . (int)$on_page;
        // =======================================================

        $result = $this->DB->query($sql, $queryParams);
        if (empty($result)) {
            throw new HttpNotFoundException("Nothing was found in $this->tableName with parameters:" . json_encode($queryParams));
        }
        foreach ($result as &$r) {
            $this->parseData($r);
        }
        return $result;
    }

    public function create(array $params): int
    {
        throw new \Exception("Model::create() need to be implemented", 501);
        return -1;
    }

    public function update(int $id, array $params): void
    {
        if (!$this->exist(['id' => $id])) {
            throw new HttpNotFoundException("$this->tableName with id=$id do not exist. You cannot update non existing collection item.");
        }

        $sql = "UPDATE $this->tableName SET";
        $queryParams = array();

        foreach ($params as $key => $value) {

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
            throw new HttpNotFoundException("$this->tableName with id=$id do not exist. You cannot delete non existing collection item.");
        }

        $this->DB->query(
            "DELETE FROM $this->tableName WHERE id=:id",
            array(':id' => $id)
        );
    }
}

class HttpNotFoundException extends \Exception
{
    public function __construct(string $message, int $code = 404)
    {
        parent::__construct($message, $code);
        $this->code = $code;
    }
}
class HttpConflictException extends \Exception
{
    public function __construct(string $message, int $code = 409)
    {
        parent::__construct($message, $code);
        $this->code = $code;
    }
}
class HttpBadRequestException extends \Exception
{
    public function __construct(string $message, int $code = 400)
    {
        parent::__construct($message, $code);
        $this->code = $code;
    }
}
