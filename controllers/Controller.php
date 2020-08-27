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
    protected $DI = null;

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
        if (
            isset($this->getQueryParam($request, 'deleted')[0]) &&
            $this->getQueryParam($request, 'deleted')[0] == 'true'
        ) {
            return true;
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
}
