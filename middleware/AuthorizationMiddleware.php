<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Psr7\Response;

/* 
checking if authenticated user have access to resources he want to perform
*/

class AuthorizationMiddleware
{
    private $accessTable = array();
    private $target = '';
    private $resourceNumber = null;
    private $Access = null;

    public function __construct(DI\Container $container)
    {
        $this->Access = $container->get("Access");
        $this->User = $container->get("User");
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $method = $request->getMethod();
        $this->target = $this->getTarget($request);

        $userAccessID = $request->getAttribute("access_id");
        $userID = $request->getAttribute("user_id");
        ["access_id" => $currentAccessID] = $this->User->read(['id' => $userID])[0];

        if ($userAccessID !== $currentAccessID) {
            throw new HttpUnauthorizedException($request,"Your access has changed - please login again");
        }

        $this->fillAccessTable($request);


        if (!isset($this->accessTable[$this->target][$method])) {
            throw new HttpMethodNotAllowedException($request, "Given Method is not allowed on this resource");
        }
        if ($this->accessTable[$this->target][$method] === false) {
            throw new HttpForbiddenException($request, "You don't have access to perform this action on given resource");
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
        }
        $resource =  array_pop($path);
        return $resource;
    }

    public function fillAccessTable(Request $request): void
    {
        list("access_id" => $accessID, "user_id" => $userID) = $request->getAttributes();
        $result = $this->Access->read(["id" => $accessID])[0];

        $sameUser = false;
        if ($this->target === 'users') {
            $sameUser = ((int) $this->resourceNumber === (int) $userID);
        }

        $this->accessTable = array(
            'statistics' => array(
                'GET' => $result['statistics_view']
                // 'POST'=>1,
                // 'PATCH'=>1,
                // 'DELETE'=>1
            ),
            'reservations' => array(
                'GET' => $result['reservations_access'],
                'POST' => $result['reservations_access'],
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
            'access' => array(
                'GET' => 1,
                'POST' => $result['access_edit'],
                'PATCH' => $result['access_edit'],
                'DELETE' => $result['access_edit']
            ),
            'rfid' =>[
                'PATCH' => $result['rfid_action']
            ]
        );
    }
}
