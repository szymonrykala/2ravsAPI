<?php

use Invoker\Exception\NotEnoughParametersException;
use Psr\Http\Message\ServerRequestInterface as Request;

use Psr\Container\ContainerInterface;
use Slim\Exception\HttpBadRequestException;

abstract class Controller
{
    protected $DIcontainer = null;

    public function __construct(ContainerInterface $DIcontainer)
    {
        $this->DIcontainer = $DIcontainer;
        $this->Log = $this->DIcontainer->get("Log");
    }

    protected function parsedQueryString(Request $request, string $queryKey = null): array
    {
        /**
         * parsing query string to get parameters into assoc array
         * 
         * @param Request $request
         * @param string $key=''
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
            $result[$key] = $regexOut[2][$num];
        }

        if (isset($queryKey)) {
            if (!isset($result[$queryKey])) return [];
            return $result[$queryKey];
        }
        return $result;
    }

    protected function getFrom(Request $request, array $rquiredParameters = array()): array
    {
        /**
         * Getting data defined in $rquiredParameters with given type
         * if $rquiredParameters is not defined, getting all params given by user
         * 
         * @param Request $request
         * @param array $rquiredParameters param => type, ...
         * 
         * @return array $data
         */
        $data = $request->getParsedBody();
        if (empty($data) || $data === NULL) {
            throw new InvalidArgumentException("Request body is empty or is not in right format", 400);
        }

        //checking required parameters
        foreach ($rquiredParameters as $param => $type) {
            if (!isset($data[$param])) {
                throw new NotEnoughParametersException("Parameter '$param' is required to perform this action", 400);
            }

            //clearing types
            $value = $data[$param];
            switch ($type) {
                case 'boolean':
                    $data[$param] = (bool) $value;
                    break;
                case 'string':
                    $data[$param] = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                    break;
                case 'integer':
                    $data[$param] = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                    break;
                case 'double':
                    $data[$param] = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT);
                    break;
            }
        }
        return $data;
    }

    protected function getSearchParams(Request $request): array
    {
        /**
         * Getting Search params from query string and from request body['search'] array if it exist
         * body['search'] have priority in values
         * 
         * @param Request $request
         * 
         * @return array $queryParams
         */
        $are_not_search_params = ['limit', 'page', 'on_page', 'ext', 'sort', 'sort_key'];
        // $queryParams = $this->parsedQueryString($request);
        $queryParams=[];

        foreach ($queryParams as $key => $value) {
            if (in_array($key, $are_not_search_params)) {
                unset($queryParams[$key]);
            }
        }

        $dataParams = $request->getParsedBody();
        if(isset($dataParams['search'])){
            extract($dataParams['search']);
            if (!isset($mode) || !isset($params)) {
                throw new HttpBadRequestException($request,"When search is enabled fields 'mode' and 'params' in search are required. Pattern:search:{mode:'REGEXP', params:{field:val,field2:val2}}");
            }
            $queryParams = array_merge($queryParams, $params);
        }else $mode = 'LIKE';

        return ['params' => $queryParams, 'mode' => $mode];
    }

    protected function switchKey(array &$array, string $oldKey, string $newKey):void{
        if(isset($array[$oldKey])){
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
        $roomMark = in_array('room_id', $extensions);
        $buildingMark = in_array('building_id', $extensions);
        $userMark = in_array('user_id', $extensions);
        $reservationMark = in_array('reservation_id', $extensions);
        $addressMark = in_array('address_id', $extensions);
        $confirmedMark = in_array('confirming_user_id', $extensions);
        $accessMark = in_array('access_id', $extensions);
        $roomTypeMark = in_array('room_type_id', $extensions);

        if ($roomMark) {
            $Room = $this->DIcontainer->get('Room');
        }
        if ($roomTypeMark) {
            $RoomType = $this->DIcontainer->get('RoomType');
        }
        if ($buildingMark) {
            $Building = $this->DIcontainer->get('Building');
        }
        if ($addressMark) {
            $Address = $this->DIcontainer->get('Address');
        }
        if ($reservationMark) {
            $Reservation = $this->DIcontainer->get('Reservation');
        }
        if ($userMark || $confirmedMark) {
            $User = $this->DIcontainer->get('User');
        }
        if ($accessMark) {
            $Access = $this->DIcontainer->get('Access');
        }

        foreach ($dataArray as &$dataEntry) {
            if ($roomMark && $dataEntry['room_id'] !== null) {
                $dataEntry['room'] = $Room->read(['id' => $dataEntry['room_id']])[0];
                unset($dataEntry['room_id']);
            }

            if ($roomTypeMark && $dataEntry['room_type_id'] !== null) {
                $dataEntry['room_type'] = $RoomType->read(['id' => $dataEntry['room_type_id']])[0];
                unset($dataEntry['room_type_id']);
            }

            if ($buildingMark && $dataEntry['building_id'] !== null) {
                $dataEntry['building'] = $Building->read(['id' => $dataEntry['building_id']])[0];
                unset($dataEntry['building_id']);
            }

            if ($addressMark && $dataEntry['address_id'] !== null) {
                $dataEntry['address'] = $Address->read(['id' => $dataEntry['address_id']])[0];
                unset($dataEntry['address_id']);
            }

            if ($reservationMark && $dataEntry['reservation_id'] !== null) {
                $dataEntry['reservation'] = $Reservation->read(['id' => $dataEntry['reservation_id']])[0];
                unset($dataEntry['reservation_id']);
            }

            if ($userMark && $dataEntry['user_id'] !== null) {
                $dataEntry['user'] = $User->read(['id' => $dataEntry['user_id']])[0];
                unset($dataEntry['user_id']);
                unset($dataEntry['user']['password']);
                unset($dataEntry['user']['action_key']);
                unset($dataEntry['user']['login_fails']);
            }

            if ($accessMark && $dataEntry['access_id'] !== null) {
                $dataEntry['access'] = $Access->read(['id' => $dataEntry['access_id']])[0];
                unset($dataEntry['access_id']);
            }

            if ($confirmedMark && $dataEntry['confirming_user_id'] !== null) {
                $dataEntry['confirming_user'] = $User->read(['id' => $dataEntry['confirming_user_id']])[0];
                unset($dataEntry['confirming_user_id']);
                unset($dataEntry['confirming_user']['password']);
                unset($dataEntry['confirming_user']['action_key']);
                unset($dataEntry['confirming_user']['login_fails']);
            } elseif (isset($dataEntry['confirmed'])) {
                $dataEntry['confirming_user_id'] = null;
            }
        }
        return $dataArray;
    }
}
