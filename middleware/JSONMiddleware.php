<?php
namespace middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Request;
use Slim\Psr7\Response;


class JSONMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request); //handling request by API

        $code = $response->getStatusCode();
        $reason = $response->getReasonPhrase();
        $body = $response->getBody();

        $responseData = null;
        if ($body !== null) {
            $decodedBody = json_decode($body);
            if (empty($decodedBody)) $responseData['message'] = (string) $body;
            else $responseData['items'] = $decodedBody;
        }

        $response = new Response();
        $response->getBody()->write(json_encode($responseData));
        $response = $response->withHeader('content-type', 'application/json')->withHeader('Access-Control-Allow-Origin','*');
        return $response->withStatus($code, $reason);
    }
}
