<?php

namespace controllers;

use Slim\Psr7\Response;
use Slim\Psr7\Request;

use Psr\Container\ContainerInterface;
use Slim\Exception\HttpBadRequestException;
use models\Log;
use models\Room;
use models\RoomType;
use models\Reservation;
use models\Building;
use models\User;
use models\Access;
use models\Address;
use models\Statistics;
use models\GenericModel;

abstract class Controller
{
    protected ContainerInterface $DIcontainer;
    protected GenericModel $Model;
    protected Log $Log;

    protected $CACHE = [];

    public function __construct(ContainerInterface $DIcontainer)
    {
        $this->DIcontainer = $DIcontainer;
        $this->Log = $this->DIcontainer->get(Log::class);
    }

    protected function parsedQueryString(Request $request, string $queryKey = null): array
    {
        /**
         * parsing query string to get parameters into assoc array
         * 
         * @param Request $request
         * @param string $key=null
         * @return array
         */
        $url = $request->getUri()->getQuery();
        $regexOut = [];
        preg_match_all('/&?([\w]*)=([:,\w-]*)/', $url, $regexOut);

        $result = [];
        foreach ($regexOut[1] as $num => $key) {
            if ($key === 'ext') {
                $result[$key] = explode(',', $regexOut[2][$num]);
                continue;
            }
            // filtering variables keys
            if (!in_array(strtolower($key), ['limit', 'page', 'on_page', 'sort', 'sort_key', 'action_key']))  continue;

            //filtering variables values
            preg_match('/[a-z0-9_,]*/', $regexOut[2][$num], $output_array);
            $result[$key] = $output_array[0];
        }

        if (isset($queryKey)) {
            if (!isset($result[$queryKey])) return [];
            return $result[$queryKey];
        }
        return $result;
    }

    protected function getParsedData(Request $request): array
    {
        $data = $request->getParsedBody();
        if (empty($data) || $data === NULL) {
            throw new HttpBadRequestException($request, "Request body is empty or is not in right format");
        }
        return $data;
    }

    protected function getSearchParams(Request $request): array
    {
        /**
         * Getting Search params from request body['search']['mode'] and body['search']['params'] array if it exist
         * 
         * @param Request $request
         * @return array $queryParams
         */
        $searchParams = $request->getParsedBody();
        if (isset($searchParams['search']) && $searchParams['search'] !== null) {
            extract($searchParams['search']);
            if (!isset($mode) || !isset($params)) {
                throw new HttpBadRequestException($request, "When search is enabled fields 'mode' and 'params' in search are required. Pattern:search:{mode:'REGEXP', params:{field:val, field2:val2}}");
            }

            if (!in_array(strtoupper($mode), ['REGEXP', 'LIKE', '=', '>', '<'])) {
                throw new HttpBadRequestException($request, 'In search, avaliable options are: REGEXP, LIKE, =, <, >');
            }
            return ['params' => $params, 'mode' => $mode];
        }
        return ['params' => null, 'mode' => null];
    }

    protected function switchKey(array &$array, string $oldKey, string $newKey): void
    {
        if (isset($array[$oldKey])) {
            $array[$newKey] = $array[$oldKey];
            unset($array[$oldKey]);
        }
    }

    protected function get(Request $request, Response $response, $args): Response
    {
        /**
         *  Getting the resources in depends on 
         * @param Request $request 
         * @param Response $response 
         * @param $args
         * 
         * @return Response 
         */
        ['params' => $params, 'mode' => $mode] = $this->getSearchParams($request);
        if (isset($params) && isset($mode))  $this->Model->setSearch($mode, $params);

        $this->Model->setQueryStringParams($this->parsedQueryString($request));

        $data = $this->handleExtensions($this->Model->read($args), $request);
        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    }

    public function getStatistics(Request $request, Response $response): Response
    {
        $Statistics = $this->DIcontainer->get(Statistics::class);
        $data = $this->getParsedData($request);
        $data['table'] = $this->Model->getTableName();

        $Statistics->loadChartData($data);

        $response->getBody()->write(json_encode($Statistics->getChartData()));
        return $response;
    }

    // ?ext=user_id,building_id,room_id...
    protected function handleExtensions(array $dataArray, Request $request): array
    {
        /**
         * Handling extensions requested by user
         * ex.: address_id extendes data with specific address data
         * 
         * @param array $dataArray
         * @param Request $request
         * 
         * @return array $dataArray 
         */
        $extensions = $this->parsedQueryString($request, 'ext');

        $arr = [
            'room_id' => [
                'new_name' => 'room',
                'Object' => in_array('room_id', $extensions) ? $this->DIcontainer->get(Room::class) : Null,
                'unset' => []
            ],
            'building_id' => [
                'new_name' => 'building',
                'Object' => in_array('building_id', $extensions) ? $this->DIcontainer->get(Building::class) : Null,
                'unset' => []
            ],
            'user_id' => [
                'new_name' => 'user',
                'Object' => in_array('user_id', $extensions) ? $this->DIcontainer->get(User::class) : Null,
                'unset' => ['user_id', 'password', 'login_fails', 'action_key']
            ],
            'reservation_id' => [
                'new_name' => 'reservation',
                'Object' => in_array('reservation_id', $extensions) ? $this->DIcontainer->get(Reservation::class) : Null,
                'unset' => []
            ],
            'address_id' => [
                'new_name' => 'address',
                'Object' => in_array('address_id', $extensions) ? $this->DIcontainer->get(Address::class) : Null,
                'unset' => []
            ],
            'confirming_user_id' => [
                'new_name' => 'confirming_user',
                'Object' => in_array('confirming_user_id', $extensions) ? $this->DIcontainer->get(User::class) : Null,
                'unset' => ['confirming_user_id', 'password', 'login_fails', 'action_key']
            ],
            'room_type_id' => [
                'new_name' => 'room_type',
                'Object' => in_array('room_type_id', $extensions) ? $this->DIcontainer->get(RoomType::class) : Null,
                'unset' => []
            ],
            'access_id' => [
                'new_name' => 'access',
                'Object' => in_array('access_id', $extensions) ? $this->DIcontainer->get(Access::class) : Null,
                'unset' => []
            ]
        ];

        $CACHERead = function ($Object, $key) {
            $cacheKey = $Object->getTableName() . implode($key);

            if (isset($this->CACHE[$cacheKey])) return $this->CACHE[$cacheKey];
            $data = $Object->read($key)[0];
            $this->CACHE[$cacheKey] = $data;
            return $data;
        };

        foreach ($dataArray as &$record) {
            foreach ($extensions as $ext) {
                if (isset($record[$ext])) {
                    $item = $CACHERead($arr[$ext]['Object'], ['id' => $record[$ext]]);
                    foreach ($arr[$ext]['unset'] as $field) unset($item[$field]);
                    $record[$arr[$ext]['new_name']] = $item;
                    unset($record[$ext]);
                }
            }
        }
        return $dataArray;
    }
}
