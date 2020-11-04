<?php

namespace models;

use utils\DBInterface;

abstract class GenericModel
{
    protected DBInterface $DB;
    protected string $tableName;
    protected array $queryStringParams = [];
    protected array $searchParams;
    protected string $searchMode = '=';

    private int $id;
    public array $data;
    protected array $SCHEMA;

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
        if (isset($params['sort_key'])  && !in_array($params['sort_key'], array_keys($this->SCHEMA)))          unset($params['sort_key']);
        if (isset($params['sort'])      && !in_array(strtoupper($params['sort']), ['DESC', 'ASC'])) unset($params['sort']);

        $this->queryStringParams = $params;
    }

    public function parseTypes(array &$readData): void
    {
        foreach ($readData as $key => &$value) {
            if (
                $value === Null &&
                isset($this->SCHEMA[$key]['nullable']) &&
                $this->SCHEMA[$key]['nullable'] === True
            ) {
                $value = Null;
            } else {
                $typedParam = new $this->SCHEMA[$key]['type']($key, $value);
                $value = $typedParam->value;
            }
        }
    }

    public function setSearch(string $mode = '=', array $params = []): void
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
        $sql = '';
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
        $sql = 'SELECT id FROM '.$this->tableName.' WHERE 1=1 ';
        ['sql' => $sqlData, 'params' => $queryParams] = $this->buildDataString($params, "=");
        $sql .= $sqlData;

        return !empty($this->DB->query($sql, $queryParams));
    }

    public function setID(int $id): void
    {
        if (!$this->exist(['id' => $id])) {
            throw new HttpNotFoundException("$this->tableName with id=$id do not exist.");
        }
        $this->id = $id;
    }
    public function getID(): int
    {
        if ($this->id) return $this->id;
        throw new \Exception($this->tableName . ' `id` is not seted yet. Use `Model->setID(int)` to set value');
    }

    public function read(array $params = []): array
    {
        /**
         * Read collection with params in param array
         *
         * @param array $params=[] reading parameters
         *
         * @throws HttpNotFoundException when nothing found
         * @return array $result
         */
        foreach ($params as $key => $value) {
            if (!isset($this->SCHEMA[$key])) unset($params[$key]);
        }

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

        if (isset($sort_key, $sort))   $sql .= ' ORDER BY '.$sort_key.' '.$sort;
        elseif (isset($sort_key))               $sql .= ' ORDER BY ' . $sort_key;
        elseif (isset($sort))                   $sql .= ' ORDER BY id ' . $sort;


        if (isset($page, $on_page)) $sql .= ' LIMIT ' . ((int)$page * (int)$on_page) . ', ' . (int)$on_page;
        elseif (isset($limit))      $sql .= ' LIMIT ' . (int)$limit;
        // =======================================================

        $result = $this->DB->query($sql, $queryParams);
        if (empty($result)) {
            throw new HttpNotFoundException('Nothing was found in '.$this->tableName.' with parameters:' . json_encode($queryParams));
        }

        //parsing types
        foreach ($result as &$resource) {
            $this->parseTypes($resource);
        }
        return $result;
    }

    public function create(array $createData): int
    {
        $SQLfields = '(';
        $SQLvalues = ' VALUES(';
        $SQLqueryData = [];

        // loop through model SCHEMA
        foreach ($this->SCHEMA as $field => $params) {

            // if field isn't required to create
            if (
                !isset($params['create']) ||
                isset($params['create']) && (bool)$params['create'] === false
            ) continue;

            // if field isn't set in data
            if (!array_key_exists($field, $createData)) {

                if (array_key_exists('default', $params)) {
                    //have defaults
                    $createData[$field] = $params['default'];
                } else {
                    throw new HttpBadRequestException('Param `' . $field . '` is required to create ' . $this->tableName);
                }
            }

            //if value can't be null but it is
            if (
                $createData[$field] === null &&
                isset($params['nullable']) &&
                (bool)$params['nullable'] === false
            ) throw new HttpBadRequestException('Param `' . $field . '` can not be null');


            if (count($SQLqueryData) >= 1) {
                $SQLfields .= ',';
                $SQLvalues .= ',';
            }
            $SQLfields .= $field;
            $SQLvalues .= ':' . $field;

            if ($createData[$field] !== null) {
                $propperType = new $this->SCHEMA[$field]['type']($field, $createData[$field]);
                $propperType->validate($params);
                $createData[$field] = $propperType->getValue();
            }
            $SQLqueryData[':' . $field] = $createData[$field];
        }
        $SQLfields .= ')';
        $SQLvalues .= ')';

        $this->DB->query('INSERT INTO ' . $this->tableName . $SQLfields . $SQLvalues, $SQLqueryData);
        return $this->DB->lastInsertID();
    }

    public function update(array $updateData, int $id = null): void
    {
        /**
         * Updating item with seted Model::id by Model::setID(int $id):void
         * 
         * @param array $updateData is assoc array [field => updateValue,...]
         * 
         */
        $sql = 'UPDATE '.$this->tableName .' SET';
        $SQLqueryData = [];

        //loop through data
        foreach ($this->SCHEMA as $field => $params) {

            //if field is not updateable
            if (
                !isset($params['update']) ||
                isset($params['update']) && $params['update'] !== true
            ) continue;

            // if user want to update filed
            if (array_key_exists($field, $updateData)) {
                // if value is null
                if ($updateData[$field] === null) {
                    // and it can be null
                    if (isset($params['nullable']) && $params['nullable']) {
                        $propperValue = null;
                    } else throw new HttpBadRequestException('Variable `' . $field . '` can not be null');
                } else { // if is not null
                    $propperType = new $this->SCHEMA[$field]['type']($field, $updateData[$field]);
                    $propperType->validate($params);
                    $propperValue = $propperType->getValue();
                    // $propperValue = 0;
                }

                count($SQLqueryData) >= 1 ? $sql .= ',' : null;
                $SQLqueryData[":$field"] = $propperValue;
                $sql .= " $field=:$field";
            }
        }
        if (!empty($SQLqueryData)) {
            $sql .= ' WHERE `id`=:id';
            $SQLqueryData[':id'] = $id ?? $this->id;
            $this->DB->query($sql, $SQLqueryData);
        }
    }

    public function delete(int $id = null): void
    {
        /**
         * Delete collection Item
         * 
         * @param int $id is optional - if not passed, the Model::id is used
         */
        $this->DB->query(
            'DELETE FROM `'.$this->tableName.'` WHERE `id`=:id',
            [':id' => $id ?? $this->id]
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

class HttpForbiddenException extends \Exception
{
    public function __construct(string $message, int $code = 403)
    {
        parent::__construct($message, $code);
        $this->code = $code;
    }
}