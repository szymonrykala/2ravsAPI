<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

/* 
checking if authenticated user have acces to resources he want to perform
*/

class AuthorizationMiddleware
{
    private $accesTable = array();
    private $target = '';
    private $Acces = null;

    public function __construct(DI\Container $container)
    {
        $this->Acces = $container->get("Acces");
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $method = $request->getMethod();
        $this->target = $this->getTarget($request);

        $userAccesID = $request->getAttribute("acces_id");
        $this->fillAccesTable($request);

        if ($this->accesTable[$this->target][$method] === false) {
            throw new AuthorizationException("You don't have acces to specified resources or method.");
        }

        $response = $handler->handle($request); //handling request by API
        $existingContent = (string) $response->getBody();
        $code = $response->getStatusCode();

        $response = new Response();
        $response->getBody()->write($existingContent);
        return $response->withHeader('content-type', 'application/json')->withStatus($code);
    }

    public function getTarget(Request $request): string
    {
        $path = explode('/', $request->getRequestTarget());
        $len = count($path) - 1;
        $target = is_numeric($path[$len]) ? $path[$len - 1] : $path[$len];
        // echo $target;

        return $target;
    }

    public function fillAccesTable(Request $request): void
    {
        list("acces_id" => $accesID, "user_id" => $userID) = $request->getAttributes();
        $result = $this->Acces->read(["id" => $accesID])[0];

        $this->accesTable = array(
            'statistics' => array(
                'GET' => $result['statistics_view']
                // 'POST'=>1,
                // 'PATCH'=>1,
                // 'DELETE'=>1
            ),
            'reservations' => array(
                'GET' => $result['reservations_acces'],
                'POST' => $result['reservations_acces'],
                'PATCH' => $result['reservations_edit'],
                'DELETE' => $result['reservations_edit']
            ),
            'confirm' => array(
                'POST' => $result['reservations_confirm']
            ),
            'buildings' => array(
                'GET' => $result['buildings_view'],
                'POST' => $result['buildings_edit'],
                'PATCH' => $result['buildings_edit'],
                'DELETE' => $result['buildings_edit']
            ),
            'rooms' => array(
                'GET' => $result['rooms_view'],
                'POST' => $result['rooms_edit'],
                'PATCH' => $result['rooms_edit'],
                'DELETE' => $result['rooms_edit']
            ),
            'users' => array(
                'GET' => 1,
                'POST' => 1,
                'PATCH' => $result['users_edit'],
                'DELETE' => $result["users_edit"]
            ),
            'logs' => array(
                'GET' => $result['logs_view'],
                // 'POST' => 1,
                // 'PATCH' => 1,
                'DELETE' => $result['logs_edit']
            ),
            'acces' => array(
                'GET' => 1,
                'POST' => $result['acces_edit'],
                'PATCH' => $result['acces_edit'],
                'DELETE' => $result['acces_edit']
            )
        );
    }
}
