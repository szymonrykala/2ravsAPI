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
    protected $DIContainer;

    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Address = $this->DIcontainer->get('Address');
    }

    // GET /addresses/{address_id}
    public function getAddress(Request $request, Response $response, $args): Response
    {
        /**
         * Getting specific address by address_id from database
         * /addresses/{address_id}
         * 
         * @param Request $request 
         * @param Response $response 
         * @param $args
         * 
         * @return Response 
         */
        $data = $this->Address->read(['id' => (int)$args['address_id']])[0];
        $response->getBody()->write(json_encode($data));
        return $response;
    }

    // GET /addresses
    public function getAllAddresses(Request $request, Response $response, $args): Response
    {
        /**
         * Getting all addresses in database
         * /addresses
         * 
         * @param Request $request 
         * @param Response $response 
         * @param $args
         * 
         * @return Response 
         */
        $data = $this->Address->read();
        $response->getBody()->write(json_encode($data));
        return $response;
    }

    // POST /addresses
    public function createAddress(Request $request, Response $response, $args): Response
    {
        /**
         * Creating new Address from request body data
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
            "message" => "User " . $request->getAttribute('user_id') . " created address id=$lastIndex"
        ]);
        return $response->withStatus(201, "Succesfully created");
    }

    // DELETE /addresses/{address_id}
    public function deleteAddress(Request $request, Response $response, $args): Response
    {
        /**
         * Deleting Addres with given id
         * /addresses/{address_id}
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
        return $response->withStatus(204, "Succesfully deleted");
    }

    //PATCH /addresses/{address_id}
    public function updateAddress(Request $request, Response $response, $args): Response
    {
        /**
         * Updating address with given id with data from request body 
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

        $dataString = implode(',', array_keys($data));
        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'message' => "User " . $request->getAttribute('email') . " updated address id=$addressID data: $dataString"
        ]);
        return $response->withStatus(204, "Succesfully updated");
    }
}
