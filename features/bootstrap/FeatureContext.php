<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Utils;

use function GuzzleHttp\json_decode;

require_once __DIR__ . '/SharedContext.php';
require_once __DIR__ . '/HttpClient.php';

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends SharedContext
{

    protected array $ids = [];

    // public function __construct()
    // {
    //     // $this->client = new HttpClient();
    // }


    /**
     * @Then access data should exist in :url list
     */
    public function accessDataShouldExistInList($url, TableNode $table)
    {
        foreach ($table as &$row) {
            if ($row['key'] === 'name') {
                $row = ['key' => $row['key'], 'value' => $row['value']];
                continue;
            }
            $row = ['key' => $row['key'], 'value' => $row['value'] === '1'];
        }

        $founded = $this->iWantToGetIdOf($url, $table);
        if ($founded) return true;
        throw new Exception('Did not find building the list');
    }

    /**
     * @When I want to login with :email and :password
     */
    public function iWantToLoginWithAnd($email, $password)
    {
        $this->client->request('POST', '/auth', [
            'email' => $email,
            'password' => $password
        ]);

        if (!$this->client->data['items']['jwt']) {
            throw new Exception('There is no authorization token');
        }

        $this->client->token =  $this->client->data['items']['jwt'];
        $this->token = $this->client->token;
    }

    /**
     * @Then write :tokenName token into file
     */
    public function writeTokenIntoFile(string $tokenName)
    {
        $tokens = json_decode(file_get_contents(__DIR__ . '/../tokens.json'), true);
        $tokens[$tokenName] = $this->token;

        if (!file_put_contents(__DIR__ . '/../tokens.json', json_encode($tokens))) {
            throw new Exception('Could not write token to file');
        };
    }

    /**
     * @When I want to update :url id data
     */
    public function iWantToUpdateIdData($url, TableNode $table)
    {
        $body = $this->buildBody($table);
        $this->client->request('PATCH', $url . $this->ids[0], $body);
    }

    /**
     * @When I want to update access :url id data
     */
    public function iWantToUpdateAccessIdData($url, TableNode $table)
    {
        $body = [];
        foreach ($table as $row) {
            if ($row['key'] === 'name') {
                $body[$row['key']] = $row['value'];
                continue;
            }
            $body[$row['key']] = $row['value'] === '1';
        }
        $this->client->request('PATCH', '/accesses/' . $this->ids[0], $body);
    }


    /**
     * @When I want to delete :url id
     */
    public function iWantToDeleteId($url)
    {
        $this->client->request('DELETE', $url . $this->ids[0]);
    }

    /**
     * @Then :url id should not exist
     */
    public function idShouldNotExist($url)
    {
        try {
            $this->client->request('GET', $url . $this->ids[0]);
        } catch (GuzzleHttp\Exception\BadResponseException $e) {
            $response = $e->getResponse();
            if ($response->getStatusCode() != 404) {
                throw $e;
            }
        }
        return true;
    }

    /**
     * @Given I am an unauthenticated user
     */
    public function iAmAnUnauthenticatedUser()
    {
        $this->client->token = '';
    }


    /**
     * @When I register user
     */
    public function iRegisterUser(TableNode $user)
    {
        if (!$this->register($user)) {
            throw new Exception('can not register user');
        }
    }

    /**
     * @Then another and behat user should be in users list
     */
    public function anotherAndBehatUserShouldBeInUsersList()
    {
        $this->client->request('GET', '/users');

        $this->contain($this->client->data['items'], 'email', $this->behatEmail);
        $this->contain($this->client->data['items'], 'email', $this->anotherEmail);
    }

    /**
     * @Given I am an authenticated :user
     */
    public function iAmAnAuthenticated(string $user)
    {
        $tokens = json_decode(file_get_contents(__DIR__ . '/../tokens.json'), true);
        $this->client->token = $tokens[$user];
    }

    /**
     * @When I want to create an access
     */
    public function iWantToCreateAnAccess(TableNode $table)
    {
        $body = [];
        foreach ($table as $row) {
            if ($row['key'] === 'name') {
                $body[$row['key']] = $row['value'];
                continue;
            }
            $body[$row['key']] = $row['value'] === '1';
        }
        $this->client->request('POST', '/accesses', $body);
    }

    /**
     * @When I activating user :email
     */
    public function iActivatingUser(string $email)
    {
        $users = $this->listOf('/users');

        $this->contain($users, 'email', $email, function ($user) {
            $this->client->request('PATCH', '/users/' . $user['id'], [
                'activated' => true
            ]);
        });
    }
    /**
     * @Then User :email should be activated
     */
    public function userShouldBeActivated(string $email)
    {
        $users = $this->listOf('/users');
        $found = $this->contain($users, 'email', $email, function ($user) {
            if ($user['activated'] !== true) {
                throw new Exception('User ' . $user['email'] . ' is not activated');
            }
            return true;
        });
        if (!$found) {
            throw new Exception('Could not find user ' . $email);
        }
    }

    /**
     * @When I want to update :email data
     */
    public function iWantToUpdateData($email,TableNode $table)
    {
        $users = $this->listOf('/users');
        $body = $this->buildBody($table);
        $user = $this->contain($users, 'email', $email);
        $this->client->request('PATCH', '/users/' . $user['id'], $body);
    }

    /**
     * @Then behat user data should be
     */
    public function behatUserDataShouldBe(TableNode $table)
    {
        $users = $this->listOf('/users');
        $user = $this->contain($users, 'email', $this->behatEmail);
        foreach ($table as $row) {
            if ($user[$row['key']] !== $row['value']) {
                throw new Exception('Values of ' . $row['key'] . ' are not the same for ' . $user['email']);
            }
        }
    }

    /**
     * @When I change :email access to :accessName
     */
    public function iChangeAccessTo(string $email, string $accessName)
    {
        $users = $this->listOf('/users');
        $accessesList = $this->listOf('/accesses');
        $accessID = $this->contain($accessesList, 'name', $accessName, function ($access) {
            return $access['id'];
        });
        if (!$accessID) throw new Exception('Did not found access ' . $accessName);

        array_push($this->ids, $accessID);

        $userID = $this->contain($users, 'email', $email, function ($user) {
            return $user['id'];
        });
        array_push($this->ids, $userID);
        if (!$userID) throw new Exception('Did not found user ' . $email);

        $this->client->request('PATCH', '/users/' . $userID, [
            'access_id' => $accessID
        ]);
        return true;
    }

    /**
     * @Then user :email should have access :field like :value
     */
    public function userShouldHaveAccessLike(string $email, string $field, $value)
    {
        $this->client->request('GET', '/users/' . $this->ids[1], [], ['ext' => 'access_id']);
        $user = $this->client->data['items'][0];

        if ($user['access'][$field] !== $value) {
            throw new Exception('user access name is not ' . $value);
        }

        return true;
    }

    /**
     * @When I getting room id by rfid code :rfidCode
     */
    public function iGettingRoomIdByRfidCode(string $rfidCode)
    {
        $this->client->request('GET', '/buildings/rooms/rfid/' . $rfidCode);
        $room = $this->client->data['items'][0];
        
        if (!$room) {
            throw new Exception('Could not get room with rfid code: ' . $rfidCode);
        }
        array_push($this->ids, $room['id']);
    }

    /**
     * @When I want to create an address
     */
    public function iWantToCreateAnAddress(TableNode $table)
    {
        $body = $this->buildBody($table);
        $this->client->request('POST', '/addresses', $body);
    }

    /**
     * @Then address should exist in addresses list
     */
    public function addressShouldExistInAddressesList(TableNode $address)
    {
        $true = 0;
        foreach ($this->listOf('/addresses') as $item) {
            foreach ($address as $val) {
                if ($item[$val['key']] == $val['value']) $true++;
            }
            if ($true === 5) return true;
            $true = 0;
        }
        throw new Exception('did not found created address');
    }


    /**
     * @When I want to create a building on this address
     */
    public function iWantToCreateABuildingOnThisAddress(TableNode $table)
    {
        $body = $this->buildBody($table);
        $body['address_id'] = $this->ids[0];

        $this->client->request('POST', '/buildings', $body);
    }

    /**
     * @Then building should exist in buildings list
     */
    public function buildingShouldExistInBuildingsList(TableNode $table)
    {
        $buildingns = $this->listOf('/buildings');
        foreach ($table as $row) {
            $this->contain($buildingns, $row['key'], $row['value'], function ($building) {
                if ($building['address_id'] != $this->ids[0]) {
                    throw new Exception('address od building should be ' . $this->ids[0]);
                }
                return true;
            });
        }
    }

    /**
     * @When I want to create a room type
     */
    public function iWantToCreateARoomType(TableNode $table)
    {
        $body = $this->buildBody($table);
        $this->client->request('POST', '/buildings/rooms/types', $body);
    }

    /**
     * @Then room type should exist in room_types list
     */
    public function roomTypeShouldExistInRoomTypesList(TableNode $table)
    {
        $types = $this->listOf('/buildings/rooms/types');
        foreach ($table as $row) {
            $item = $this->contain($types, $row['key'], $row['value']);
        }
        if ($item) {
            return true;
        }
        throw new Exception('Did not find room type n the list');
    }

    /**
     * @When I want to create a room
     */
    public function iWantToCreateARoom(TableNode $table)
    {
        $body = $this->buildBody($table);
        $body['room_type_id'] = $this->ids[1];
        $body['seats_count'] = (int) $body['seats_count'];
        $body['floor'] = (int) $body['floor'];

        $body['equipment'] = explode(';', $body['equipment']);
        $this->client->request('POST', '/buildings/' . $this->ids[0] . '/rooms', $body);
    }


    /**
     * @Then Building should have :arg1 room
     */
    public function buildingShouldHaveRoom($arg1)
    {
        $building = $this->listOf('/buildings/' . $this->ids[0])[0];
        if ($building['rooms_count'] !== 1) throw new Exception('building rooms_count is not equal to ' . $arg1);

        $rooms = $this->listOf('/buildings/' . $this->ids[0] . '/rooms');
        if (count($rooms) === (int)$arg1) return true;
        throw new Exception('Created building have more or less room than ' . $arg1);
    }

    /**
     * @When I getting a list of rooms in building :arg1
     */
    public function iGettingAListOfRoomsInBuilding($arg1)
    {
        $buildings = $this->listOf('/buildings');
        $rooms_list = $this->contain($buildings, 'name', $arg1, function ($building) {
            return $this->listOf('/buildings/' . $building['id'] . '/rooms');
        });
        if (!empty($rooms_list)) {
            $this->roomsList = $rooms_list;
            return true;
        }
        throw new Exception('didn\'t found building with given name');
    }

    /**
     * @Then in list should be only one room named :arg1
     */
    public function inListShouldBeOnlyOneRoomNamed($arg1)
    {
        if (count($this->roomsList) === 1) {
            if ($this->roomsList[0]['name'] !== $arg1) {
                throw new Exception('Room name is not ' . $arg1);
            }
            return true;
        } else throw new Exception('room is not equal to ' . $arg1);
    }

    /**
     * @Then :url id should have data
     */
    public function idShouldHaveData($url, TableNode $table)
    {
        $resource = $this->listOf($url . $this->ids[0])[0];
        foreach ($table as $row) {
            if ($resource[$row['key']] != $row['value']) {
                throw new Exception('in updated ' . $url . ' ' . $row['key'] . ' is ' . $resource[$row['key']]);
            }
        }
    }

    /**
     * @Then access :url id should have data
     */
    public function accessIdShouldHaveData($url, TableNode $table)
    {
        foreach ($table as &$row) {
            if ($row['key'] === 'name') continue;
            $row['value'] = (bool) $row['value'];
        }
        $this->idShouldHaveData($url, $table);
    }


    /**
     * @Then room should be blocked :blocked
     */
    public function roomShouldBeBlocked($blocked)
    {
        $room = $this->listOf('/buildings/rooms/' . $this->ids[0])[0];
        if ($room['blockade'] !== (bool) $blocked) {
            throw new Exception('Room is blocakde = ' . $blocked);
        }
    }


    /**
     * @When I want to unblock the room
     */
    public function iWantToUnblockTheRoom()
    {
        $this->client->request("PATCH", '/buildings/rooms/' . $this->ids[0], [
            'blockade' => false
        ]);
    }


    /**
     * @When I want to make a reservation
     */
    public function iWantToMakeAReservation(TableNode $table)
    {
        $body = $this->buildBody($table);
        $this->client->request('POST', '/buildings/' . $this->ids[0] . '/rooms/' . $this->ids[1] . '/reservations', $body);
        $this->client->request('GET', '/reservations');
    }

    /**
     * @When I want to confirm this reservation
     */
    public function iWantToConfirmThisReservation()
    {
        $this->client->request('PATCH', '/reservations/' . $this->ids[0], [
            'confirmed' => true
        ]);
    }

    /**
     * @Then reservation should be confirmed :confirmed
     */
    public function reservationShouldBeConfirmed($confirmed)
    {
        $reservation = $this->listOf('/reservations/' . $this->ids[0])[0];
        if ($reservation['confirmed'] !== ($confirmed === '1')) {
            throw new Exception('reservation confirmed is = ' . $confirmed);
        }
    }
}
