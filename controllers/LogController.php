<?php

namespace controllers;

use models\GenericModel;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Response;
use Slim\Psr7\Request;
use Slim\Exception\HttpBadRequestException;
use models\Log;

class LogController extends Controller
{
    /**
     * Implement endpoints related with logs routs
     * 
     */
    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Model = $this->DIcontainer->get(Log::class);
    }

    // GET /logs
    // GET /logs/{id}
    public function getLogs(Request $request, Response $response, $args): Response
    {
        /**
         * Getting all logs from database
         * GET /logs
         * 
         * @param Request $request 
         * @param Response $response 
         * @param $args
         * 
         * @return Response 
         */
        $this->switchKey($args, 'log_id', 'id');
        return parent::get($request,$response,$args);
    }

    // DELETE /logs/{log_id}
    // if log_id < 0:
    /* {    "IDs":[log_id,log_id,log_id,log_id,log_id,...]    } */
    public function deleteLogByID(Request $request, Response $response, $args): Response
    {
        /**
         * Deleting Logs by id from /logs/{log_id} or request body "IDs":[] when log_id<0
         * DELETE /logs/{log_id}
         * { "IDs":[log_id,log_id,log_id,log_id,log_id,...] }
         * 
         * @param Request $request 
         * @param Response $response 
         * @param $args
         * 
         * @return Response 
         */
        $logID = (int) $args['log_id'];
        if ($logID < 0) {
            $data = $request->getParsedBody();

            if ($data == null || !isset($data['ids'])) {
                throw new HttpBadRequestException($request, "No data has been passed - 'ids' param is required when 'log_id' in query string is below 0.");
            }
            foreach ($data['ids'] as $logID) {
                $this->Model->data['id'] = $logID;
                $this->Model->delete();
            }
        } else {
            $this->Model->data['id'] = $logID;
            $this->Model->delete();
        }

        return $response->withStatus(204, "Deleted");
    }
}
