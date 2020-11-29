<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Utils;

class HttpClient
{
    protected Response $response;
    protected string $baseUrl;
    public string $token = '';
    public int $code = 0;
    public $data;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 10
        ]);
        $this->baseUrl = 'http://localhost:8080/v1';
    }

    private function setResponse(Response $response)
    {
        $this->code = $response->getStatusCode();
        $this->phrase = $response->getReasonPhrase();
        $data = (string)$response->getBody();
        $this->data = json_decode($data, true);
    }

    public function request(string $method, string $URI, array $body = [], array $queyString = [])
    {

        // var_dump('Bearer ' . $this->token);
        $response = $this->client->request($method, $this->baseUrl . $URI, [
            'json' => $body,
            'query' => $queyString,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ]);

        $this->setResponse($response);
    }
}
