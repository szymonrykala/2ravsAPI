<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Psr7\Response;

/* 
checking if authenticated user have acces to resources he want to perform
*/

class AuthorizationMiddleware
{
    private $accesTable = array();
    private $target = '';
    private $resourceNumber = null;
    private $Acces = null;

    public function __construct(DI\Container $container)
    {
        $this->Acces = $container->get("Acces");
        $this->User = $container->get("User");
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $method = $request->getMethod();
        $this->target = $this->getTarget($request);

        $userAccesID = $request->getAttribute("acces_id");
        $userID = $request->getAttribute("user_id");
        list("acces_id" => $currentAccesID) = $this->User->read(['id' => $userID])[0];

        if ($userAccesID !== $currentAccesID) {
            throw new HttpUnauthorizedException($request,"Your acces has changed - please login again");
        }

        $this->fillAccesTable($request);


        if (!isset($this->accesTable[$this->target][$method])) {
            throw new HttpMethodNotAllowedException($request, "Given Method is not allowed on this resource");
        }
        if ($this->accesTable[$this->target][$method] === false) {
            throw new HttpForbiddenException($request, "You don't have acces to perform this action on given resource");
        }

        $response = $handler->handle($request); //handling request by API
        $existingContent = (string) $response->getBody();
        $code = $response->getStatusCode();
        $reason = $response->getReasonPhrase();

        $response = new Response();
        $response->getBody()->write($existingContent);
        return $response->withStatus($code, $reason);
    }

    public function getTarget(Request $request): string
    {
        $uri = explode('?', $request->getRequestTarget())[0];
        $path = explode('/', $uri);
        $len = count($path) - 1;
        if (is_numeric($path[$len])) {
            $this->resourceNumber = array_pop($path);
        } elseif ($path[$len] == 'search') {
            array_pop($path);
        }
        $resource =  array_pop($path);
        return $resource;
    }

    public function fillAccesTable(Request $request): void
    {
        list("acces_id" => $accesID, "user_id" => $userID) = $request->getAttributes();
        $result = $this->Acces->read(["id" => $accesID])[0];

        $sameUser = false;
        if ($this->target === 'users') {
            $sameUser = ((int) $this->resourceNumber === (int) $userID);
        }

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
            'addresses' => [
                'GET' => $result['buildings_view'],
                'POST' => $result['buildings_edit'],
                'PATCH' => $result['buildings_edit'],
                'DELETE' => $result['buildings_edit']
            ],
            'rooms' => array(
                'GET' => $result['rooms_view'],
                'POST' => $result['rooms_edit'],
                'PATCH' => $result['rooms_edit'],
                'DELETE' => $result['rooms_edit']
            ),
            'types' => array(
                'GET' => $result['rooms_view'],
                'POST' => $result['rooms_edit'],
                'PATCH' => $result['rooms_edit'],
                'DELETE' => $result['rooms_edit']
            ),
            'users' => array(
                'GET' => true,
                'POST' => true,
                'PATCH' => $sameUser || $result['users_edit'],
                'DELETE' => $sameUser || $result["users_edit"]
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
