<?php

use Psr\Http\Message\ResponseInterface as Response;

interface ViewInterface
{
     public static function setError(Response $response, string $errorMessage, int $errorCode): Response;
     public static function setSucces(Response $response, string $message, array $data=array()): Response;
}

class View implements ViewInterface
{
     public static function setError(Response $response, string $message, int $code): Response
     {
          $arr = array(
               'succes' => false,
               'errorMessage' => $message,
               'errorCode' => $code,
          );
          $response->getBody()->write(json_encode($arr));
          return $response->withHeader('content-type', 'application/json')
          ->withStatus($code);
     }

     public static function setSucces(Response $response, string $message, array $data=array()): Response
     {
          $arr = array(
               'succes' => true,
               'message' => $message,
               'data' => $data,
          );
          $response->getBody()->write(json_encode($arr));
          return $response->withHeader('content-type', 'application/json');
     }
}
