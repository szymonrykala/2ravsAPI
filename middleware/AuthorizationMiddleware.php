<?php

namespace middleware;

use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpUnauthorizedException;
use models\Access;
use models\User;

/* 
checking if authenticated user have access to resources he want to perform
*/

class AuthorizationMiddleware
{
    private array $accessTable;
    private string $target;
    private int $resourceNumber;
    private Access $Access;

    public function __construct(\DI\Container $container)
    {
        $this->Access = $container->get(Access::class);
        $this->User = $container->get(User::class);
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $method = $request->getMethod();
        $this->setTarget($request->getRequestTarget());

        $this->User->data = $this->User->read(['id' => $request->getAttribute("user_id")])[0];

        if ($request->getAttribute("access_id") !== $this->User->data['access_id']) {
            throw new HttpUnauthorizedException($request, "Your access has changed - please login again");
        }

        $this->fillAccessTable($request);


        if (!isset($this->accessTable[$this->target][$method])) {
            throw new HttpMethodNotAllowedException($request, "Given Method is not allowed on this resource");
        }

        if ($this->accessTable[$this->target][$method] === false) {
            throw new HttpForbiddenException($request, "You don't have access to perform this action on given resource");
        }

        $response = $handler->handle($request); //handling request by API

        $newResponse = new Response();
        $newResponse->getBody()->write((string)$response->getBody());
        return $newResponse->withStatus($response->getStatusCode(), $response->getReasonPhrase());
    }

    public function setTarget(string $URL): void
    {
        [$URI, $queryString] = explode('?', $URL);
        $arr = explode('/', $URI);

        $item = array_pop($arr);
        if (is_numeric($item))  $this->resourceNumber = $item;
        $this->target = array_pop($arr);
    }

    public function fillAccessTable(Request $request): void
    {
        list("access_id" => $accessID, "user_id" => $userID) = $request->getAttributes();
        $result = $this->Access->read(["id" => $accessID])[0];

        $sameUser = false;
        if ($this->target === 'users') {
            $sameUser = ($this->resourceNumber === $userID);
        }

        $this->accessTable = array(
            'statistics' => array(
                'GET' => $result['statistics_view']
            ),
            'reservations' => array(
                'GET' => $result['reservations_access'],
                'POST' => $result['reservations_access'],
                'PATCH' => $result['reservations_edit'],
                'DELETE' => $result['reservations_edit']
            ),
            'confirm' => array(
                'PATCH' => $result['reservations_confirm']
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
                'DELETE' => $result['logs_edit']
            ),
            'access' => array(
                'GET' => 1,
                'POST' => $result['access_edit'],
                'PATCH' => $result['access_edit'],
                'DELETE' => $result['access_edit']
            ),
            'rfid' => [
                'GET' => $result['rfid_action'],
                'PATCH' => $result['rfid_action'],
            ]
        );
    }
}
