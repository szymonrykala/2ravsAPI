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
    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Model = $DIcontainer->get(Access::class);
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
        $this->switchKey($args, 'access_id', 'id');
        return parent::get($request,$response,$args);
    }

    public function createNewAccessClass(Request $request, Response $response, $args): Response
    {
        /**
         * Creating access type in database
         * POST /access/{access_id}
         * 
         * @param Request $request
         * @param Response $response
         * @param array $args
         * 
         * @return Response $response
         */

        $data = $this->getParsedData($request);

        $data['id'] = $this->Model->create($data);
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
         * 
         * @param Request $request
         * @param Response $response
         * @param array $args
         * 
         * @return Response $response
         */
        $this->Model->data = $this->Model->read(['id' => $args['access_id']])[0];

        $data = $this->getParsedData($request);

        $this->Model->update($data);

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
        $this->Model->data = $this->Model->read(['id' => $args['access_id']])[0];
        $this->Model->delete();

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'message' => 'USER ' . $request->getAttribute('email') . ' DELETE access DATA ' . json_encode($this->Model->data)
        ]);
        return $response->withStatus(204, "Deleted");
    }
}
