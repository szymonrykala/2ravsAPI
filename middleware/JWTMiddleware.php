<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use Nowakowskir\JWT\TokenEncoded;
use Nowakowskir\JWT\JWT;

class JWTMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $token = $this->recieveToken($request);
        list(
            "user_id" => $userID,
            "acces_id" => $accesID,
            "email" => $email,
            "ex" => $exTime
        ) = $this->getData($token);

        if ($exTime < time()) {
            throw new AuthorizationException("token has been expired");
        }

        $request = $request
            ->withAttribute('user_id', $userID)
            ->withAttribute('acces_id', $accesID)
            ->withAttribute('email', $email);

        $response = $handler->handle($request); //handling request by API

        $existingContent = (string) $response->getBody();
        $response = new Response();
        $response->getBody()->write($existingContent);
        return $response;
    }

    public function recieveToken(Request $request): string
    {
        $authorization = $request->getHeader('Authorization');
        if (empty($authorization)) {
            throw new AuthorizationException('No Authorization header');
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
            throw new AuthorizationException("JWT: " . $e->getMessage(), 401);
        }
        return $tokenData;
    }
}
