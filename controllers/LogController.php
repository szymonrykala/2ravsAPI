<?php
namespace controllers;
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
    public $Log = null;

    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Log = $this->DIcontainer->get(Log::class);
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
        ['params' => $params, 'mode' => $mode] = $this->getSearchParams($request);
        if (isset($params) && isset($mode))  $this->Log->setSearch($mode, $params);

        $this->Log->setQueryStringParams($this->parsedQueryString($request));

        $this->switchKey($args, 'log_id', 'id');
        $data = $this->handleExtensions($this->Log->read($args), $request);

        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
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

            if ($data == null || !isset($data['IDs'])) {
                throw new HttpBadRequestException($request,"No data has been passed - 'IDs' param is required when 'log_id' in query string is below 0.");
            }
            foreach ($data['IDs'] as $logID) {
                $this->Log->delete($logID);
            }
        } else {
            $this->Log->delete($logID);
        }

        return $response->withStatus(204, "Deleted");
    }
}
