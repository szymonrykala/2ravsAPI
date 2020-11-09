<?php

namespace models;

use utils\types\MyInt;
use utils\types\MyString;

final class Statistics extends GenericModel
{

    protected array $SCHEMA = [
        'id' => [
            'type' => MyInt::class,
        ],
        'name' => [
            'type' => MyString::class,
        ],
        'rfid' => [
            'type' => MyString::class,
        ],
        'floor' => [
            'type' => MyInt::class,
        ],
        'email' => [
            'type' => MyString::class,
        ],
        'date' => [
            'type' => MyString::class,
        ],
        'surname' => [
            'type' => MyString::class,
        ],
        'reservations_count' => [
            'type' => MyInt::class,
        ],
        'reserved_time' => [
            'type' => MyString::class,
        ],
        'address_id' => [
            'type' => MyInt::class
        ],
        'room_type_id' => [
            'type' => MyInt::class
        ],
        'room_id' => [
            'type' => MyInt::class
        ],
        'building_id' => [
            'type' => MyInt::class
        ]
    ];

    private $chartsTables = [
        'rooms' => [
            'fields' => ['id', 'name', 'room_type_id', 'rfid', 'floor'],
            'ref' => 'room_id'
        ],
        'buildings' => [
            'fields' => ['id', 'name', 'address_id'],
            'ref' => 'building_id'
        ],
        'users' => [
            'fields' => ['id', 'name', 'surname', 'email'],
            'ref' => 'user_id'
        ],
        'reservations' => [
            'fields' => ['id','date'],
            'ref' => 'id'
        ]
    ];

    public function loadChartData(array $data)
    {
        $this->date = $data['date'];
        $this->time = $data['time'];
        $this->order = $data['order'] ?? ['key' => 'id', 'direction' => 'DESC'];
        $this->group = $data['group'] ?? 'id';
        $this->limit = $data['limit'] ?? 100;
        $this->table = $data['table'] ?? 'reservations';
    }

    private function getFields(string $table): string
    {
        array_push($this->chartsTables[$table]['fields'],$this->group);
        
        $sql = '';
        foreach ($this->chartsTables[$table]['fields'] as $field) {
            $sql .= '`' . $table . '`.`' . $field . '` as ' . $field . ', ';
        }
        $sql .= 'COUNT(`reservations`.`id`) as reservations_count,
            COALESCE( TIME_FORMAT( SUM(TIMEDIFF(`end_time`, `start_time`)), \'%H:%i:%s\'), \'00:00:00\') as reserved_time';
        return $sql;
    }

    private function buildQuery(): array
    {
        $queryData = [];
        $sql = 'SELECT ' . $this->getFields($this->table);
        $sql .= ' FROM `' . $this->table . '` ';

        if ($this->table !== 'reservations') {
            $sql .= ' INNER JOIN `reservations` ON `' . $this->table . '`.`id`=`reservations`.`' . $this->chartsTables[$this->table]['ref'] . '` ';
        } 
        $sql .= ' WHERE 1=1 ';

        if ($this->date) {
            $sql .= ' AND `date` BETWEEN :dateFrom AND :dateTo ';
            $queryData[':dateFrom'] = ($this->date['from'] ?? '2000-01-01');
            $queryData[':dateTo'] = ($this->date['to'] ?? date('Y-m-d'));
        }
        if ($this->time) {
            $sql .= ' AND `time` BETWEEN :timeFrom AND :timeTo ';
            $queryData[':timeFrom'] = ($this->time['from'] ?? '01:00');
            $queryData[':timeTo'] = ($this->date['to'] ?? '24:00');
        }

        $sql .= ' GROUP BY `' . $this->table . '`.`' . $this->group . '`';
        $sql .= ' ORDER BY ' . ($this->order['key'] ?? 'id') . ' ' . $this->order['direction'] ?? 'DESC';
        $sql .= ' LIMIT ' . $this->limit;

        return [$sql, $queryData];
    }

    public function getChartData(): array
    {
        [$sql, $params] = $this->buildQuery();

        $data = $this->DB->query($sql, $params);
        foreach ($data as &$record) $this->parseTypes($record);
        return $data;
    }
}
