<?php

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

    protected function generateToken(int $userID, int $accesID, string $email): string
    {
        //creating new token
        $time = time();
        $tokenDecoded = new TokenDecoded(
            ['typ' => 'JWT', 'alg' => JWT::ALGORITHM_HS384],
            array(
                'user_id' => $userID,
                'acces_id' => $accesID,
                'email' => $email,
                'ex' => $time + (60 * 60) * 25 //valid 250hours??
            )
        );
        // encoding the token
        $tokenEncoded = $tokenDecoded->encode(JWT_SIGNATURE, JWT::ALGORITHM_HS384);
        return $tokenEncoded->__toString();
    }

    protected function getRandomKey(int $len): string
    {
        return base64_encode(random_bytes($len));
    }

    protected function deleted($request): bool
    {
        if (isset($this->getQueryParam($request, 'deleted')[0])) {
            $var = $this->getQueryParam($request, 'deleted')[0];
            if ($var === 'true' || $var === '1') {
                return true;
            }
        }
        return false;
    }

    protected function getQueryParam(Request $request, string $key): array
    {
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
        $data = $request->getParsedBody();
        if (empty($data) || $data === NULL) {
            throw new IncorrectRequestBodyException();
        }

        //checking required parameters
        foreach ($rquiredParameters as $param => $type) {
            if (!isset($data[$param])) {
                throw new RequiredParameterException($rquiredParameters);
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

    // ?ext=user_id,building_id,room_id...
    protected function handleExtensions(array $dataArray, Request $request): array
    {
        $extensions = $this->getQueryParam($request, 'ext');

        $roomMark = false;
        $buildingMark = false;
        $userMark = in_array('user_id', $extensions);
        $reservationMark = false;
        $confirmedMark = in_array('confirming_user_id', $extensions);

        if (in_array('room_id', $extensions)) {
            $Room = $this->DIcontainer->get('Room');
            $roomMark = true;
        }
        if (in_array('building_id', $extensions)) {
            $Building = $this->DIcontainer->get('Building');
            $buildingMark = true;
        }
        if (in_array('reservation_id', $extensions)) {
            $Reservation = $this->DIcontainer->get('Reservation');
            $reservationMark = true;
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
