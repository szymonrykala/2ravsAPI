<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use GuzzleHttp\Exception\GuzzleException;

require_once __DIR__ . '/HttpClient.php';

/**
 * Defines application features from the specific context.
 */
class SharedContext implements Context
{

    protected HttpClient $client;

    protected string $adminEmail;
    protected string $adminPassword;

    protected string $behatEmail;
    protected string $behatPassword;

    protected string $anotherEmail;
    protected string $anotherPassword;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->client = new HttpClient();
    }

    protected function register($table)
    {
        $body = [];
        foreach ($table as $row) {
            $body[$row['key']] = $row['value'];
        }
        try {
            $this->client->request('POST', '/users', $body);
        } catch (GuzzleHttp\Exception\ServerException $e) {
            echo "Mail was not send";
            return true;
        }
        if ($this->client->code == 201) {
            return true;
        } else return false;
    }

    protected function listOf(string $path): array
    {
        $this->client->request('GET', $path);
        // var_dump($this->client->data);
        return $this->client->data['items'];
    }

    /**
     * Building body message from table in Table node
     * 
     * @param TableNode $table behat table
     * @return array body message
    */
    protected function buildBody(TableNode $table): array
    {
        $body = [];
        foreach ($table as $row) {
            $body[$row['key']] = $row['value'];
        }
        return $body;
    }

    /**
     * Check if item with $key = $value exists in $array
     * if it is, then execute callback function if is defined with founded item as param
     * else it return founded item
     * 
     * @param array &array
     * @param string $key
     * @param $vlaue
     * @param callable callback = null
     * 
     * @return $item  - founded item in array with $key = $value
     * @throws Exception if item found
    */
    protected function contain(array &$array, string $key, $value, $callback = null)
    {
        foreach ($array as $item) {
            if (isset($item[$key]) && $item[$key] === $value) {
                if (isset($callback)) return $callback($item);
                else return $item;
            }
        }
        throw new Exception('can not found ' . $key . ' with value ' . $value);
    }

    /**
     * @When I want to get id of :url
     */
    public function iWantToGetIdOf($url, TableNode $table)
    {
        $true = 0;
        $len = count($table->getTable()) - 1;
        foreach ($this->listOf($url) as $item) {
            foreach ($table as $row) {
                if ($item[$row['key']] == $row['value']) $true += 1;
            }
            if ($true === $len) {
                array_push($this->ids, $item['id']);
                return true;
            };
            $true = 0;
        }
        throw new Exception('didn\'t get id of ' . $url);
    }

    /**
     * @Then data should exist in :url list
     */
    public function dataShouldExistInList($url, TableNode $table)
    {
        $founded = $this->iWantToGetIdOf($url, $table);
        if ($founded) return true;
        throw new Exception('Did not find building the list');
    }

}
