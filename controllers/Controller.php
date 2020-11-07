<?php

namespace controllers;

use Invoker\Exception\NotEnoughParametersException;
use Psr\Http\Message\ServerRequestInterface as Request;

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

abstract class Controller
{
    protected ContainerInterface $DIcontainer;

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
         * 
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
                'present' => ($room_ext = in_array('room_id', $extensions)),
                'new_name' => 'room',
                'Object' => ($room_ext) ? $this->DIcontainer->get(Room::class) : Null,
                'unset' => ['room_id']
            ],
            'building_id' => [
                'present' => ($building_ext = in_array('building_id', $extensions)),
                'new_name' => 'building',
                'Object' => ($building_ext) ? $this->DIcontainer->get(Building::class) : Null,
                'unset' => ['building_id']
            ],
            'user_id' => [
                'present' => ($user_ext = in_array('user_id', $extensions)),
                'new_name' => 'user',
                'Object' => $user_ext ? $this->DIcontainer->get(User::class) : Null,
                'unset' => ['user_id', 'password', 'login_fails', 'action_key']
            ],
            'reservation_id' => [
                'present' => ($reservation_ext = in_array('reservation_id', $extensions)),
                'new_name' => 'reservation',
                'Object' => $reservation_ext ? $this->DIcontainer->get(Reservation::class) : Null,
                'unset' => ['reservation_id']
            ],
            'address_id' => [
                'present' => ($address_ext = in_array('address_id', $extensions)),
                'new_name' => 'address',
                'Object' => $address_ext ? $this->DIcontainer->get(Address::class) : Null,
                'unset' => ['address_id']
            ],
            'confirming_user_id' => [
                'present' => ($conf_ext = in_array('confirming_user_id', $extensions)),
                'new_name' => 'confirming_user',
                'Object' => $conf_ext ? $this->DIcontainer->get(User::class) : Null,
                'unset' => ['confirming_user_id', 'password', 'login_fails', 'action_key']
            ],
            'room_type_id' => [
                'present' => ($room_type_ext = in_array('room_type_id', $extensions)),
                'new_name' => 'room_type',
                'Object' => $room_type_ext ? $this->DIcontainer->get(RoomType::class) : Null,
                'unset' => ['room_type_id']
            ],
            'access_id' => [
                'present' => ($access_ext = in_array('access_id', $extensions)),
                'new_name' => 'access',
                'Object' => $access_ext ? $this->DIcontainer->get(Access::class) : Null,
                'unset' => ['access_id']
            ]
        ];

        foreach ($dataArray as &$record) {
            foreach ($arr as $key => $params) {
                if (!$params['present']) continue;

                $item = $params['Object']->read(['id' => $record[$key]])[0];

                foreach ($params['unset'] as $field) unset($record[$field]);
                
                $record[$params['new_name']] = $item;
            }
        }
        return $dataArray;
    }
}
