<?php

namespace utils;

use models\GenericModel;
use models\HttpBadRequestException;

class ModelRead
{
    private string $tableName;
    private array $SCHEMA;

    private array $configs = [];

    private $engine;

    public function __construct(GenericModel $model)
    {
        $this->SCHEMA = $model->getSchema();
        $this->tableName = $model->getTableName();

        $this->engine = function ($params) {
            return $this->read($params);
        };
    }

    public function getConfigs(): array
    {
        return $this->configs;
    }

    public function setConfigs(array $configs): void
    {
        if (isset($configs['sort']) && isset($configs['sort_key'])) {
            if (!in_array($configs['sort_key'], array_keys($this->SCHEMA))) {
                throw new HttpBadRequestException('Given `sort_key` is not valid key in ' . $this->tableName);
            }

            if (!in_array(strtolower($configs['sort']), ['asc', 'desc'])) {
                throw new HttpBadRequestException('Sort value have to be `asc` or `desc`');
            }

            $this->configs['sorting'] = ' ORDER BY `' . $configs['sort_key'] . '` ' . $configs['sort'];
        } elseif (isset($configs['sort'])) {
            $this->configs['sorting'] = ' ORDER BY `id` ' . $configs['sort'];
        }

        if (isset($configs['page'], $configs['on_page'])) {

            foreach (['page', 'on_page'] as $key) {
                if (!is_numeric($configs[$key])) throw new HttpBadRequestException('Value of `' . $key . '` have to be numeric');
            }

            $this->configs['paging'] = ' LIMIT ' . ($configs['page'] * $configs['on_page']) . ', ' . $configs['on_page'];
        } elseif (isset($configs['limit'])) {
            if (!is_numeric($configs['limit'])) throw new HttpBadRequestException('Value of `limit` have to be numeric');
            $this->configs['paging'] = ' LIMIT ' . $configs['limit'];
        }
    }

    private function applyReadConfigs(string $sql): string
    {
        foreach ($this->configs as $config => $string) {
            $sql .= $string;
        }
        return $sql;
    }

    private function read(array $params): array
    {
        $sql = 'SELECT * FROM `' . $this->tableName . '`';
        $sqlParams = [];
        $readParams = [];
        foreach ($params as $key => $value) {
            if (!isset($this->SCHEMA[$key])) continue;

            array_push($readParams, $key . '=:' . $key);
            $sqlParams[':' . $key] = $value;
        }
        if (!empty($readParams)) $sql .= ' WHERE ' . join(' AND ', $readParams);

        return [$sql, $sqlParams];
    }

    private function search(array $params): array
    {
        /**
         * {
         *      "key":{
         *          "value": sdtgr,
         *          "mode" : regexp | LIKE | = | > | <
         *      },
         *      ...
         * }
         */
        $sql = 'SELECT * FROM `' . $this->tableName . '`';
        $sqlParams = [];
        $readParams = [];
        foreach ($params as $param => ['mode' => $mode, 'value' => $value]) {
            if (!isset($this->SCHEMA[$param])) continue;

            array_push($readParams, $param . ' ' . $mode . ' :' . $param);
            $sqlParams[':' . $param] = $value;
        }
        if (!empty($readParams)) $sql .= ' WHERE ' . join(' AND ', $readParams);

        return [$sql, $sqlParams];
    }

    public function switchToSearch(): void
    {
        $this->engine = function ($params) {
            return $this->search($params);
        };
    }

    public function getSQL(array $params): array
    {
        $engineFunc = $this->engine;
        [$sql, $sqlParams] = $engineFunc($params);

        return [$this->applyReadConfigs($sql), $sqlParams];
    }
}
