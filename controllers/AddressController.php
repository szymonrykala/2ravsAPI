<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . "/Controller.php";

class AddressController extends Controller
{
    /**
     * Implement endpoints related with saddresses paths
     * 
     */
    private $Address;

    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Address = $this->DIcontainer->get('Address');
    }

    // GET /addresses
    // GET /addresses/{id}
    public function getAddresses(Request $request, Response $response, $args): Response
    {
        /**
         * Getting all addresses in database
         * GET /addresses  OR  GET /addresses/{address_id}
         * 
         * @param Request $request 
         * @param Response $response 
         * @param $args
         * 
         * @return Response 
         */
        ['params' => $params, 'mode' => $mode] = $this->getSearchParams($request);
        if (isset($params) && isset($mode))  $this->Address->setSearch($mode, $params);

        $this->Address->setQueryStringParams($this->parsedQueryString($request));

        $this->switchKey($args,'address_id','id');
        $data = $this->Address->read($args);

        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    }

    // POST /addresses
    public function createAddress(Request $request, Response $response, $args): Response
    {
        /**
         * Creating new Address from request body data
         * POST /addresses
         * {
         *      "country":"",
         *      "town":"",
         *      "postal_code":"",
         *      "street":"",
         *      "number":""
         * } 
         * 
         * @param Request $request 
         * @param Response $response 
         * @param $args
         * 
         * @return Response 
         */
        $data = $this->getFrom($request, [
            'country' => 'string',
            'town' => 'string',
            'postal_code' => 'string',
            'street' => 'string',
            'number' => 'string'
        ]);

        $lastIndex = $this->Address->create($data);
        $this->Log->create([
            "user_id" => $request->getAttribute('user_id'),
            "message" => "User " . $request->getAttribute('user_id') . " created address id=$lastIndex; data:" . json_encode($data)
        ]);
        return $response->withStatus(201, "Created");
    }

    // PATCH /addresses/{address_id}
    public function updateAddress(Request $request, Response $response, $args): Response
    {
        /**
         * Updating address with given id with data from request body 
         * PATCH /addresses/{address_id}
         *  
         * @param Request $request 
         * @param Response $response 
         * @param $args
         * 
         * @return Response 
         */
        $data = $this->getFrom($request);
        $addressID = $args['address_id'];
        $this->Address->update($addressID, $data);

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'message' => "User " . $request->getAttribute('email') . " updated address id=$addressID data:" . json_encode($data)
        ]);
        return $response->withStatus(204, "Updated");
    }

    // DELETE /addresses/{address_id}
    public function deleteAddress(Request $request, Response $response, $args): Response
    {
        /**
         * Deleting Addres with given id
         * DELETE /addresses/{address_id}
         *
         * @param Request $request 
         * @param Response $response 
         * @param $args
         * 
         * @return Response 
         */
        $this->Address->delete((int)$args['address_id']);
        $this->Log->create([
            'user_id' => (int)$request->getAttribute('user_id'),
            'message' => "User " . $request->getAttribute('email') . " deleted Address id=" . $args['address_id']
        ]);
        return $response->withStatus(204, "Deleted");
    }
}
