<?php

use Invoker\Exception\NotEnoughParametersException;
use Psr\Http\Message\ServerRequestInterface as Request;

use \Nowakowskir\JWT\JWT;
use \Nowakowskir\JWT\Base64Url;
use Nowakowskir\JWT\TokenDecoded;
use Nowakowskir\JWT\TokenEncoded;
use Nowakowskir\JWT\Exceptions\UnsecureTokenException;
use Psr\Container\ContainerInterface;

abstract class Controller
{
    protected $DIcontainer = null;

    public function __construct(ContainerInterface $DIcontainer)
    {
        $this->DIcontainer = $DIcontainer;
        $this->Log = $this->DIcontainer->get("Log");
    }

    protected function deleted(Request $request): bool
    {
        /**
         * checking if deleted flag param is ste to '1' or 'true' in query string
         * 
         * @param Request $request
         * 
         * @return bool $deleted
         */
        if (isset($this->getQueryParam($request, 'deleted')[0])) {
            $var = $this->getQueryParam($request, 'deleted')[0];
            if ($var === 'true' || $var === '1') {
                return true;
            }
        }
        return false;
    }

    protected function getQueryParam(Request $request, string $key = null): array
    {
        /**
         * Getting query param from query string
         * getting params if $key is not defined 
         * 
         * @param Request $request
         * @param string $key 
         * 
         * @return array $param
         */
        $result = array();
        $queryString = $request->getUri()->getQuery();

        $params = explode('&', $queryString);

        foreach ($params as $params) {

            $paramArr = explode('=', $params);
            preg_match('/=/', $params, $array);
            if (!empty($array)) {

                $key = $paramArr[0];
                $value = $paramArr[1];

                $valueArr = explode(',', $value);
                preg_match('/,/', $value, $array);

                if (!empty($array)) {
                    $value = $valueArr;
                }
                $result[$key] = $value;
            }
        }
        if (isset($result[$key])) {
            return is_array($result[$key]) ? $result[$key] : array($result[$key]);
        } else {
            return array();
        }
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
        $queryString = $request->getUri()->getQuery();

        preg_match_all('/(\w+)=([A-z0-9]+)/', $queryString, $regexOut);
        $queryParams = [];
        foreach ($regexOut[1] as $number => $key) {
            $queryParams[$key] = $regexOut[2][$number];
        }

        $dataParams = $request->getParsedBody();
        if (isset($dataParams['search']) & is_array($dataParams['search'])) {
            foreach ($dataParams['search'] as $key => $value) {
                $queryParams[$key] = $value;
            }
        }

        return $queryParams;
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
        $extensions = $this->getQueryParam($request, 'ext');

        $roomMark = in_array('room_id', $extensions);
        $buildingMark = in_array('building_id', $extensions);
        $userMark = in_array('user_id', $extensions);
        $reservationMark = in_array('reservation_id', $extensions);
        $addressMark = in_array('address_id', $extensions);
        $confirmedMark = in_array('confirming_user_id', $extensions);

        if ($roomMark) {
            $Room = $this->DIcontainer->get('Room');
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

        foreach ($dataArray as &$dataEntry) {
            if ($roomMark && $dataEntry['room_id'] !== null) {
                $dataEntry['room'] = $Room->read(['id' => $dataEntry['room_id']])[0];
                unset($dataEntry['room_id']);
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
