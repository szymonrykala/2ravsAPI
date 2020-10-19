<?php
namespace controllers;

use models\HttpConflictException;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Response;
use Slim\Psr7\Request;
use Slim\Exception\HttpBadRequestException;
use models\Access;
use models\User;
use utils\Validator;


class AccessController extends Controller
{
    private Access $Access;

    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Access = $DIcontainer->get(Access::class);
    }

    public function validateAccess(Request $request, array &$data): void
    {
        /**
         * Validate Access
         * 
         * @param array $data
         * @throws HttpBadRequestException
         */
        $Validator = $this->DIcontainer->get(Validator::class);
        if (isset($data['name'])) {
            if (!$Validator->validateString($data['name'])) {
                throw new HttpBadRequestException($request, 'Incorrect access name format; pattern: '.$Validator->clearString);
            }
            $data['name'] = $Validator->sanitizeString($data['name']);
        }
    }

    // GET /access
    // GET /access/{id}
    public function getAccessTypes(Request $request, Response $response, $args): Response
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

    public function createNewAccessType(Request $request, Response $response, $args): Response
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

        $data = $this->getFrom($request, [
            "name" => 'string',
            "access_edit" => 'boolean',
            "buildings_view" => 'boolean',
            "buildings_edit" => 'boolean',
            "logs_view" => 'boolean',
            "logs_edit" => 'boolean',
            "rooms_view" => 'boolean',
            "rooms_edit" => 'boolean',
            "reservations_access" => 'boolean',
            "reservations_confirm" => 'boolean',
            "reservations_edit" => 'boolean',
            "users_edit" => 'boolean',
            "statistics_view" => 'boolean',
        ], true);

        $this->validateAccess($request, $data);

        $newID = $this->Access->create($data);
        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'message' => 'User ' . $request->getAttribute('email') . ' created new access class id=' . $newID . ' data:' . json_encode($data)
        ]);
        return $response->withStatus(201, "Created");
    }

    public function updateAccessType(Request $request, Response $response, $args): Response
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

        $data = $this->getFrom($request, [
            "name" => 'string',
            "access_edit" => 'boolean',
            "buildings_view" => 'boolean',
            "buildings_edit" => 'boolean',
            "logs_view" => 'boolean',
            "logs_edit" => 'boolean',
            "rooms_view" => 'boolean',
            "rooms_edit" => 'boolean',
            "reservations_access" => 'boolean',
            "reservations_confirm" => 'boolean',
            "reservations_edit" => 'boolean',
            "users_edit" => 'boolean',
            "statistics_view" => 'boolean',
        ], false);

        $this->validateAccess($request, $data);

        $this->Access->update($args['access_id'], $data);
        $this->Log->create([
            "user_id" => $request->getAttribute('user_id'),
            "message" => "User " . $request->getAttribute('email') . " updated data:" . json_encode($data)
        ]);
        return $response->withStatus(204, "Updated");
    }

    public function deleteAccessType(Request $request, Response $response, $args): Response
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
        // each user with current access have to 
        $User = $this->DIcontainer->get(User::class);
        if ($User->exist(['access_id' => $args['access_id']])) {
            throw new HttpConflictException("Some Users stil have this access class. You can't delete it", 409); //conflict
        } else {
            $this->Access->delete($args['access_id']);
            $this->Log->create([
                "user_id" => $request->getAttribute('user_id'),
                "message" => "User " . $request->getAttribute('email') . " deleted access id=" . $args['access_id']
            ]);
        }
        return $response->withStatus(204, "Deleted");
    }
}
