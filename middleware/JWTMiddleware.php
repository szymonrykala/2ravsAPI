<?php

namespace middleware;

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
    private int $userID;
    private int $accessID;
    private string $email;
    private int $assigned;
    private string $ip;

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
            ($this->assigned < (time() - $this->JWTsettings['valid_time']))
        ) throw new TokenExpiredException("Token has been expired", 401);

        if (
            $this->JWTsettings['ip_controll'] && $this->ip !== getHostByName(getHostName())
        ) throw new TokenInactiveException("Token has incorrect informations.", 401);

        $request = $this->request
            ->withAttribute('user_id', $this->userID)
            ->withAttribute('access_id', $this->accessID)
            ->withAttribute('email', $this->email);

        $response = $handler->handle($request); //handling request by API

        $code = $response->getStatusCode();
        $reason = $response->getReasonPhrase();
        $existingContent = (string) $response->getBody();

        $response = new Response();
        $response->getBody()->write($existingContent);
        return $response->withStatus($code, $reason);
    }

    public function recieveToken(): string
    {
        $authorization = $this->request->getHeader('Authorization');
        if (empty($authorization)) {
            throw new HttpUnauthorizedException($this->request, "No authorization header found");
        }

        return explode(' ', $authorization[0])[1];
    }

    public function loadData(string $token): void
    {
        try {
            $tokenEncoded = new TokenEncoded($token);
            $tokenEncoded->validate($this->JWTsettings['signature'], JWT::ALGORITHM_HS512);
            $tokenData = (array) $tokenEncoded->decode()->getPayload();
        } catch (\Exception $e) {
            throw new HttpUnauthorizedException($this->request, "JWT: " . $e->getMessage());
        }
        if (!isset($tokenData['user_id'],
        $tokenData['access_id'],
        $tokenData['email'],
        $tokenData['assigned'],
        $tokenData['ip'])) throw new TokenInactiveException("Token has incorrect informations.", 401);

        $this->userID = (int)$tokenData['user_id'];
        $this->accessID = (int)$tokenData['access_id'];
        $this->email = (string)$tokenData['email'];
        $this->assigned = (int)$tokenData['assigned'];
        $this->ip = (string)$tokenData['ip'];
    }
}
