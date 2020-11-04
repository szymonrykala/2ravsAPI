<?php

namespace controllers;

use Psr\Container\ContainerInterface;
use Slim\Psr7\Response;
use Slim\Psr7\Request;
use Slim\Exception\HttpBadRequestException;
use models\Address;

class AddressController extends Controller
{
    /**
     * Implement endpoints related with saddresses paths
     * 
     */
    private Address $Address;

    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Address = $this->DIcontainer->get(Address::class);
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

        $this->switchKey($args, 'address_id', 'id');
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

        $data = $this->getParsedData($request);

        $data['id'] = $this->Address->create($data);
        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'message' => 'USER ' . $request->getAttribute('user_id') . ' CREATE address DATA ' . json_encode($data)
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

        $data = $this->getParsedData($request);

        $this->Address->setID($args['address_id']);
        $this->Address->update($data);

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'message' => 'USER ' . $request->getAttribute('email') . ' UPDATE address DATA ' . json_encode($data)
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
        $address = $this->Address->read(['id' => $args['address_id']])[0];

        $this->Address->delete($args['address_id']);
        $this->Log->create([
            'user_id' => (int)$request->getAttribute('user_id'),
            'message' => 'USER ' . $request->getAttribute('email') . ' DELETE address DATA ' . json_encode($address)
        ]);
        return $response->withStatus(204, "Deleted");
    }
}
