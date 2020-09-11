<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require_once __DIR__ . "/Controller.php";

class LogController extends Controller
{
    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
    }

    // GET /logs
    public function getAllLogs(Request $request, Response $response, $args): Response
    {
        $data = $this->handleExtensions($this->Log->read(), $request);
        $response->getBody()->write(json_encode($data));
        return $response;
    }

    // DELETE /logs/{log_id}
    // if log_id < 0:
    /* {    "IDs":[log_id,log_id,log_id,log_id,log_id,...]    } */
    public function deleteLogByID(Request $request, Response $response, $args): Response
    {
        $logID = (int) $args['log_id'];
        if ($logID < 0) {
            $data = $request->getParsedBody();

            if ($data == null || !isset($data['IDs'])) {
                throw new APIException("No data has been passed - 'IDs' required while log_id<0", 400);
            }
            foreach ($data['IDs'] as $logID) {
                $this->Log->delete($logID);
            }
        } else {
            $this->Log->delete($logID);
        }

        return $response->withStatus(204, "Succesfully deleted");
    }


    public function searchLogs(Request $request, Response $response, $args): Response
    {
        /**
         * Searching for Logs with parameters given in Request(query string or body['search'])
         * Founded results are written into the response body
         * 
         * @param Request $request 
         * @param Response $response 
         * @param $args
         * 
         * @return Response 
         */
        $params = $this->getSearchParams($request);

        $data = $this->Log->search($params);

        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    }
}
