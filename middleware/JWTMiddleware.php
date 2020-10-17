<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use Nowakowskir\JWT\TokenEncoded;
use Nowakowskir\JWT\JWT;
use Slim\Exception\HttpUnauthorizedException;

class JWTMiddleware
{
    private $request = null;

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $this->request = $request;
        $token = $this->recieveToken();
        list(
            "user_id" => $userID,
            "access_id" => $accessID,
            "email" => $email,
            "ex" => $exTime
        ) = $this->getData($token);

        // if ($exTime < time()) {
        //     throw new TokenExpiredException("Token has been expired", 401);
        // }

        $request = $this->request
            ->withAttribute('user_id', $userID)
            ->withAttribute('access_id', $accessID)
            ->withAttribute('email', $email);

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

    public function getData(string $token): array
    {
        try {
            $tokenEncoded = new TokenEncoded($token);
            $tokenEncoded->validate(JWT_SIGNATURE, JWT::ALGORITHM_HS384);
            $tokenData = (array) $tokenEncoded->decode()->getPayload();
        } catch (Exception $e) {
            throw new HttpUnauthorizedException($this->request, "JWT: " . $e->getMessage());
        }
        return $tokenData;
    }
}
