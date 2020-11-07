<?php

namespace controllers;

use models\HttpConflictException;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Response;
use Slim\Psr7\Request;
use Slim\Exception\HttpBadRequestException;
use models\Access;
use models\User;


class AccessController extends Controller
{
    private Access $Access;

    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Access = $DIcontainer->get(Access::class);
    }

    // GET /access
    // GET /access/{id}
    public function getAccessClass(Request $request, Response $response, $args): Response
    {
        /**
         * Getting access types from database
         * GET /access
         * GET /access/{access_id}
         * 
         * @param Request $request
         * @param Response $response
         * @param array $args
         * 
         * @return Response $response
         */
        ['params' => $params, 'mode' => $mode] = $this->getSearchParams($request);
        if (isset($params) && isset($mode))  $this->Access->setSearch($mode, $params);

        $this->Access->setQueryStringParams($this->parsedQueryString($request));

        $this->switchKey($args, 'access_id', 'id');
        $data = $this->Access->read($args);

        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    }

    public function createNewAccessClass(Request $request, Response $response, $args): Response
    {
        /**
         * Creating access type in database
         * POST /access/{access_id}
         * {
         *     "name":"",
         *     "rfid_action":"",
         *     "access_edit":"",
         *     "buildings_view":"",
         *     "buildings_edit":"",
         *     "logs_view":"",
         *     "logs_edit":"",
         *     "rooms_view":"",
         *     "rooms_edit":"",
         *     "reservations_access":"",
         *     "reservations_confirm":"",
         *     "reservations_edit":"",
         *     "users_edit":"",
         *     "statistics_view":""
         * } 
         * 
         * @param Request $request
         * @param Response $response
         * @param array $args
         * 
         * @return Response $response
         */

        $data = $this->getParsedData($request);

        $data['id'] = $this->Access->create($data);
        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'message' => 'USER ' . $request->getAttribute('email') . ' CREATE access DATA ' . json_encode($data)
        ]);
        return $response->withStatus(201, "Created");
    }

    public function updateAccessClass(Request $request, Response $response, $args): Response
    {
        /**
         * Updating access type by given access_id
         * PATCH /access/{access_id}
         * {
         *     "name":"",
         *     "access_edit":"",
         *     "buildings_view":"",
         *     "buildings_edit":"",
         *     "logs_view":"",
         *     "logs_edit":"",
         *     "rooms_view":"",
         *     "rooms_edit":"",
         *     "reservations_access":"",
         *     "reservations_confirm":"",
         *     "reservations_edit":"",
         *     "users_edit":"",
         *     "statistics_view":""
         * }
         * 
         * @param Request $request
         * @param Response $response
         * @param array $args
         * 
         * @return Response $response
         */
        $this->Access->data = $this->Access->read(['id' => $args['access_id']])[0];

        $data = $this->getParsedData($request);

        $this->Access->update($data);

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'message' => 'USER ' . $request->getAttribute('email') . ' UPDATE access DATA ' . json_encode($data)
        ]);
        return $response->withStatus(204, "Updated");
    }

    public function deleteAccessClass(Request $request, Response $response, $args): Response
    {
        /**
         * Deleting access type by access_id
         * DELETE /access/{access_id}
         * 
         * @param Request $request
         * @param Response $response
         * @param array $args
         * 
         * @return Response $response
         */
        $this->Access->data = $this->Access->read(['id' => $args['access_id']])[0];
        $this->Access->delete();

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'message' => 'USER ' . $request->getAttribute('email') . ' DELETE access DATA ' . json_encode($this->Access->data)
        ]);
        return $response->withStatus(204, "Deleted");
    }
}
