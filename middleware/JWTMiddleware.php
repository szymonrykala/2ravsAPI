<?php

namespace middleware;

use Nowakowskir\JWT\Exceptions\EmptyTokenException;
use Nowakowskir\JWT\Exceptions\TokenExpiredException;
use Nowakowskir\JWT\Exceptions\TokenInactiveException;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

use Nowakowskir\JWT\TokenEncoded;
use Nowakowskir\JWT\JWT;
use Slim\Exception\HttpUnauthorizedException;

class JWTMiddleware
{
    private Request $request;
    private array $JWTsettings;
    private array $data;

    public function __construct(array $JWTsettings)
    {
        $this->JWTsettings = $JWTsettings;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $this->request = $request;
        $this->loadData($this->recieveToken());

        if (
            $this->JWTsettings['is_expire'] &&
            ($this->data['assigned'] < (time() - $this->JWTsettings['valid_time']))
        ) throw new TokenExpiredException("Token has been expired", 401);

        if (
            $this->JWTsettings['ip_controll'] && $this->data['ip'] !== getHostByName(getHostName())
        ) throw new TokenInactiveException("Token has invalid data", 401);

        $request = $this->request
            ->withAttribute('user_id', $this->data['user_id'])
            ->withAttribute('access_id', $this->data['access_id'])
            ->withAttribute('email', $this->data['email']);

        $response = $handler->handle($request); //handling request by API

        $code = $response->getStatusCode();
        $reason = $response->getReasonPhrase();
        $existingContent = (string) $response->getBody();

        $response = new Response();
        $response->getBody()->write($existingContent);
        return $response->withStatus($code, $reason);
    }

    private function recieveToken(): string
    {
        $authorizationHeader = explode(' ', $this->request->getHeader('Authorization')[0]);

        if (strtolower($authorizationHeader[0]) !== 'bearer' || !isset($authorizationHeader[1])) {
            throw new HttpUnauthorizedException($this->request, "No authorization header found");
        }

        return $authorizationHeader[1];
    }

    private function loadData(string $token): void
    {
        try {
            $tokenEncoded = new TokenEncoded($token);
            $tokenEncoded->validate($this->JWTsettings['signature'], JWT::ALGORITHM_HS512);
            $tokenData = (array) $tokenEncoded->decode()->getPayload();
        } catch (\Exception $e) {
            throw new HttpUnauthorizedException($this->request, "JWT: " . $e->getMessage());
        }

        if (
            !isset($tokenData['user_id'], $tokenData['access_id'], $tokenData['email'], $tokenData['assigned'], $tokenData['ip'])
        ) throw new EmptyTokenException('Token has invalid data', 401);

        $this->data['user_id'] = (int)$tokenData['user_id'];
        $this->data['access_id'] = (int)$tokenData['access_id'];
        $this->data['email'] = (string)$tokenData['email'];
        $this->data['assigned'] = (int)$tokenData['assigned'];
        $this->data['ip'] = (string)$tokenData['ip'];
    }
}
