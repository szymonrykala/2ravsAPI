<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;


class JSONMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request); //handling request by API

        $code = $response->getStatusCode();
        $reason = $response->getReasonPhrase();
        $body = $response->getBody();
        if ($body !== null) {
            $body = json_decode($body);
        }
        $data = ['succes' => true, 'data' => $body];

        $response = new Response();
        $response->getBody()->write(json_encode($data));
        $response = $response->withHeader('content-type', 'application/json');
        return $response->withStatus($code, $reason);
    }
}
