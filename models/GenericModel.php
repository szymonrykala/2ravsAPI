<?php

namespace models;

use utils\DBInterface;
use utils\ModelRead;

abstract class GenericModel
{
    protected DBInterface $DB;
    protected string $tableName;
    protected array $SCHEMA;
    public array $data;

    public function __construct(DBInterface $DBInterface)
    {
        $this->DB = $DBInterface;
        $this->DB->connect();
        $this->Reader = new ModelRead($this);
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getSchema(): array
    {
        return $this->SCHEMA;
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
                $value = $this->SCHEMA[$key]['type']::parseType($value);
            }
        }
    }

    public function exist(array $params): bool
    {
        /**
         * check is exist model with given params
         *
         * @param array $params
         * @return bool
         */
        $sql = 'SELECT id FROM ' . $this->tableName . ' WHERE 1=1 ';
        $queryParams = [];
        $sqlParams = [];

        foreach ($params as $param => $value) {
            $sqlParams[':' . $param] = $value;
            array_push($queryParams, $param . '=:' . $param);
        }
        $sql .= join(' AND ', $queryParams);

        return !empty($this->DB->query($sql, $sqlParams));
    }


    public function fieldUpdatePolicy(string $field, array &$updateData): bool
    {
        /**
         * Check if field can be updated
         * 
         * @param array &$updateData reference
         * @param string $filed as field name
         * @throws HttpBadRequestException
         * @return bool true when succes in applying policies
         */
        if (
            !array_key_exists($field, $updateData) || // user don't want to update filed
            (!isset($this->SCHEMA[$field]['update']) || $this->SCHEMA[$field]['update'] !== true) // field is not updateable
        ) return false;

        // if value is null
        if ($updateData[$field] === null) {
            // and it can not be null
            if (
                !isset($this->SCHEMA[$field]['nullable']) ||
                $this->SCHEMA[$field]['nullable'] !== true
            ) throw new HttpBadRequestException('Variable `' . $field . '` can not be null');

            $updateData[$field] = null;
        } else { // if is not null
            $propperType = new $this->SCHEMA[$field]['type']($field, $updateData[$field]);
            $propperType->applyRules($this->SCHEMA[$field]);
            $updateData[$field] = $propperType->getValue();
        }
        return true;
    }

    public function fieldCreatePolicy(string $field, array &$createData): bool
    {
        /**
         * Check if field can be created
         * 
         * @param array &$createData reference
         * @param string $filed as field name
         * @throws HttpBadRequestException
         * @return bool true when succes in applying policies
         */

        // if field isn't required to create
        if (
            (!isset($this->SCHEMA[$field]['create']) || (bool)$this->SCHEMA[$field]['create'] !== true)
        ) return false;

        // if field isn't set in data
        if (!array_key_exists($field, $createData)) {

            if ( //if there is no default param
                !array_key_exists('default', $this->SCHEMA[$field])
            ) throw new HttpBadRequestException('Param `' . $field . '` is required to create ' . $this->tableName);

            $createData[$field] = $this->SCHEMA[$field]['default'];
            return true;
        }

        //if value can't be null but it is
        if (
            $createData[$field] === null &&
            isset($params['nullable']) && $params['nullable'] !== true
        ) throw new HttpBadRequestException('Param `' . $field . '` can not be null');


        $propperType = new $this->SCHEMA[$field]['type']($field, $createData[$field]);
        $propperType->applyRules($this->SCHEMA[$field]);
        $createData[$field] = $propperType->getValue();
        return true;
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

        [$sql, $sqlParams] = $this->Reader->getSQL($params);
        $result = $this->DB->query($sql, $sqlParams);
        if (empty($result)) {
            throw new HttpNotFoundException('Nothing was found in ' . $this->tableName . ' with parameters:' .
                json_encode(array_merge($params, $this->Reader->getConfigs())));
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

            // if policies are not applied, continue to next field
            if (!$this->fieldCreatePolicy($field, $createData)) continue;

            if (!empty($SQLqueryData)) {
                $SQLfields .= ',';
                $SQLvalues .= ',';
            }
            $SQLfields .= $field;
            $SQLvalues .= ':' . $field;
            $SQLqueryData[':' . $field] = $createData[$field];
        }
        $SQLfields .= ')';
        $SQLvalues .= ')';

        $this->DB->query('INSERT INTO ' . $this->tableName . $SQLfields . $SQLvalues, $SQLqueryData);
        return $this->DB->lastInsertID();
    }

    public function update(array $updateData): void
    {
        /**
         * Updating item with seted Model::id by Model::setID(int $id):void
         * 
         * @param array $updateData is assoc array [field => updateValue,...]
         * 
         */
        $sql = 'UPDATE ' . $this->tableName . ' SET';
        $SQLqueryData = [];

        //loop through data
        foreach ($this->SCHEMA as $field => $params) {

            // if policies are not applied, continue to next field
            if (!$this->fieldUpdatePolicy($field, $updateData)) continue;

            !empty($SQLqueryData) ? $sql .= ',' : null;
            $SQLqueryData[":$field"] = $updateData[$field];
            $sql .= " $field=:$field";
        }

        if (!empty($SQLqueryData)) {
            $sql .= ' WHERE `id`=:id';
            $SQLqueryData[':id'] = $this->data['id'];
            $this->DB->query($sql, $SQLqueryData);
        }
    }

    public function delete(): void
    {
        /**
         * Delete collection Item
         * 
         * @param int $id is optional - if not passed, the Model::id is used
         */
        $this->DB->query(
            'DELETE FROM `' . $this->tableName . '` WHERE `id`=:id',
            [':id' => $this->data['id']]
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
