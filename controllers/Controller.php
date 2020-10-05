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

    protected function getFrom(Request $request, array $parameters, bool $required = false): array
    {
        /**
         * Getting data defined in $parameters with given type
         * 
         * @param Request $request
         * @param array $parameters param => type, ...
         * 
         * @return array $data - requested parameters with requested type
         */
        $data = $request->getParsedBody();
        if (empty($data) || $data === NULL) {
            throw new InvalidArgumentException("Request body is empty or is not in right format", 400);
        }

        //skipping unnessesry values
        $outputData = [];
        foreach ($data as $key => $value) {
            if (!in_array($key, array_keys($parameters))) continue;
            if (gettype($value) !== $parameters[$key]) {
                throw new HttpBadRequestException($request, "Bad variable type passed. Variable '$key' need to be a type of " . $parameters[$key]);
            }
            if (
                $parameters[$key] === 'string' &&
                !filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^[\w\s@\.,:-]+$/u']])
            ) {
                throw new HttpBadRequestException($request, "Incorrect variable value. Variable '$key' has incorrect value; pattern:/^[\w\s@\.,:-]+$/u");
            }

            $outputData[$key] = $value;
        }
        if ($required) {
            //checking required parameters
            foreach ($parameters as $param => $type) {
                if (!isset($outputData[$param])) {
                    throw new NotEnoughParametersException("Parameter '$param' with typeof '$type' is required to perform this action", 400);
                }
            }
        }
        return $outputData;
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
