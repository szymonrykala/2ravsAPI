Feature: Service pipeline
    Testing all main features of the app


    @users @create @unauthenticated @registration
    Scenario: register users
        Given I am an unauthenticated user
        When I register user
            | key      | value           |
            | name     | Behat           |
            | surname  | testingBehat    |
            | email    | behat@gmail.com |
            | password | BehatPass12!$   |
        And I register user
            | key      | value             |
            | name     | Another           |
            | surname  | anotherBehat      |
            | email    | another@gmail.com |
            | password | BehatAnother12!$  |
        And I want to login with "szymonrykala@gmail.com" and "Rolekskejt121$"
        Then data should exist in "/users" list
            | key   | value             |
            | email | another@gmail.com |
        Then data should exist in "/users" list
            | key   | value           |
            | email | behat@gmail.com |

    @users @token
    Scenario: getting admin auth tokens
        Given I am an unauthenticated user
        When I want to login with "szymonrykala@gmail.com" and "Rolekskejt121$"
        Then write "admin" token into file

    @users @admin
    Scenario Outline: activating the users
        Given I am an authenticated "admin"
        When I activating user "<email>"
        Then User "<email>" should be activated

        Examples:
            | email             |
            | behat@gmail.com   |
            | another@gmail.com |

    @users @token
    Scenario Outline: getting auth tokens
        Given I am an unauthenticated user
        When I want to login with "<email>" and "<password>"
        Then write "<tokenName>" token into file

        Examples:
            | email                  | password         | tokenName        |
            | behat@gmail.com        | BehatPass12!$    | behatUser        |
            | another@gmail.com      | BehatAnother12!$ | anotherBehatUser |


    @access @create @admin
    Scenario: creating behat admin access class
        Given I am an authenticated "admin"
        When I want to create an access
            | key                  | value        |
            | name                 | behat access |
            | rfid_action          | 1            |
            | access_edit          | 1            |
            | buildings_view       | 1            |
            | buildings_edit       | 1            |
            | logs_view            | 1            |
            | logs_edit            | 0            |
            | rooms_view           | 1            |
            | rooms_edit           | 0            |
            | reservations_access  | 1            |
            | reservations_confirm | 0            |
            | reservations_edit    | 0            |
            | users_edit           | 0            |
            | statistics_view      | 1            |
        Then access data should exist in "/accesses" list
            | key                  | value        |
            | name                 | behat access |
            | rfid_action          | 1            |
            | access_edit          | 1            |
            | buildings_view       | 1            |
            | buildings_edit       | 1            |
            | logs_view            | 1            |
            | logs_edit            | 0            |
            | rooms_view           | 1            |
            | rooms_edit           | 0            |
            | reservations_access  | 1            |
            | reservations_confirm | 0            |
            | reservations_edit    | 0            |
            | users_edit           | 0            |
            | statistics_view      | 1            |


    @access @update @admin
    Scenario: creating behat admin access class
        Given I am an authenticated "admin"
        When I want to get id of "/accesses"
            | key  | value        |
            | name | behat access |
        And I want to update access "/accesses/" id data
            | key                  | value               |
            | name                 | behat access update |
            | logs_edit            | 1                   |
            | rooms_edit           | 1                   |
            | reservations_confirm | 1                   |
            | reservations_edit    | 1                   |
            | users_edit           | 1                   |
        Then access "/accesses/" id should have data
            | key                  | value               |
            | name                 | behat access update |
            | rfid_action          | 1                   |
            | access_edit          | 1                   |
            | buildings_view       | 1                   |
            | buildings_edit       | 1                   |
            | logs_view            | 1                   |
            | logs_edit            | 1                   |
            | rooms_view           | 1                   |
            | rooms_edit           | 1                   |
            | reservations_access  | 1                   |
            | reservations_confirm | 1                   |
            | reservations_edit    | 1                   |
            | users_edit           | 1                   |
            | statistics_view      | 1                   |


    @users @behatUser @update
    Scenario: normal user update his data
        Given I am an authenticated "behatUser"
        When I want to get id of "/users"
            | key   | value           |
            | email | behat@gmail.com |
        And I want to update "/users/" id data
            | key     | value              |
            | surname | updateSurnameBehat |
            | name    | updateNameBehat    |
        Then "/users/" id should have data
            | key     | value              |
            | surname | updateSurnameBehat |
            | name    | updateNameBehat    |


    @users @update @admin
    Scenario: admin updating behat user access
        Given I am an authenticated "admin"
        When I change "behat@gmail.com" access to "behat access update"
        Then user "behat@gmail.com" should have access "name" like "behat access update"


    Scenario Outline: Get auth token after access change
        Given I am an unauthenticated user
        When I want to login with "<email>" and "<password>"
        Then write "<tokenName>" token into file
        Examples:
            | email           | password      | tokenName |
            | behat@gmail.com | BehatPass12!$ | behatUser |


    @address @create @behatUser
    Scenario: creating address
        Given I am an authenticated "behatUser"
        When I want to create an address
            | key         | value     |
            | country     | Poland    |
            | town        | Bydgoszcz |
            | postal_code | 85-796    |
            | street      | Behata    |
            | number      | 34b       |
        Then address should exist in addresses list
            | key         | value     |
            | country     | Poland    |
            | town        | Bydgoszcz |
            | postal_code | 85-796    |
            | street      | Behata    |
            | number      | 34b       |


    @buildings @create @behatUser
    Scenario: creating building
        Given I am an authenticated "behatUser"
        When I want to get id of "/addresses"
            | key         | value     |
            | country     | Poland    |
            | town        | Bydgoszcz |
            | postal_code | 85-796    |
            | street      | Behata    |
            | number      | 34b       |
        And I want to create a building on this address
            | key  | value          |
            | name | Behat Building |
        Then building should exist in buildings list
            | key  | value          |
            | name | Behat Building |


    @room_types @create @behatUser
    Scenario: create a room type
        Given I am an authenticated "behatUser"
        When I want to create a room type
            | key  | value           |
            | name | test behat type |
        Then data should exist in "/buildings/rooms/types" list
            | key  | value           |
            | name | test behat type |


    @rooms @create @behatUser
    Scenario: creating room
        Given I am an authenticated "behatUser"
        When I want to get id of "/buildings"
            | key  | value          |
            | name | Behat Building |
        And I want to get id of "/buildings/rooms/types"
            | key  | value           |
            | name | test behat type |
        And I want to create a room
            | key         | value                  |
            | name        | B.behat room           |
            | seats_count | 60                     |
            | floor       | 1                      |
            | equipment   | umywalka;kreda;tablica |
            | rfid        | kgsui843itu459gufd     |
        Then data should exist in "/buildings/rooms" list
            | key  | value        |
            | name | B.behat room |
        And Building should have "1" room


    @rfid @getting @behatUser
    Scenario: getting room by rfid code
        Given I am an authenticated "behatUser"
        When I getting room id by rfid code "kgsui843itu459gufd"
        Then "/buildings/rooms/" id should have data
            | key         | value        |
            | name        | B.behat room |
            | seats_count | 60           |
            | floor       | 1            |
        And room should be blocked "1"


    @rooms @behatUser @read
    Scenario: getting a room from building
        Given I am an authenticated "behatUser"
        When I getting a list of rooms in building "Behat Building"
        Then in list should be only one room named "B.behat room"


    @address @update @behatUser
    Scenario: update a address
        Given I am an authenticated "behatUser"
        When I want to get id of "/addresses"
            | key         | value     |
            | country     | Poland    |
            | town        | Bydgoszcz |
            | postal_code | 85-796    |
            | street      | Behata    |
            | number      | 34b       |
        And I want to update "/addresses/" id data
            | key     | value         |
            | street  | Behata Update |
            | country | Poland update |
        Then "/addresses/" id should have data
            | key         | value         |
            | country     | Poland update |
            | town        | Bydgoszcz     |
            | postal_code | 85-796        |
            | street      | Behata Update |
            | number      | 34b           |


    @buildings @update @behatUser
    Scenario: update a building
        Given I am an authenticated "behatUser"
        When I want to get id of "/buildings"
            | key  | value          |
            | name | Behat Building |
        And I want to update "/buildings/" id data
            | key  | value                 |
            | name | Behat Building update |
        Then "/buildings/" id should have data
            | key  | value                 |
            | name | Behat Building update |


    @room_types @update @behatUser
    Scenario: update a room type
        Given I am an authenticated "behatUser"
        When I want to get id of "/buildings/rooms/types"
            | key  | value           |
            | name | test behat type |
        And I want to update "/buildings/rooms/types/" id data
            | key  | value                  |
            | name | behat room type update |
        Then "/buildings/rooms/types/" id should have data
            | key  | value                  |
            | name | behat room type update |


    @rooms @update @behatUser
    Scenario: updating a room
        Given I am an authenticated "behatUser"
        When I want to get id of "/buildings/rooms"
            | key   | value        |
            | name  | B.behat room |
            | floor | 1            |
        And I want to update "/buildings/rooms/" id data
            | key  | value               |
            | name | B.behat room update |
        Then "/buildings/rooms/" id should have data
            | key         | value               |
            | name        | B.behat room update |
            | seats_count | 60                  |
            | rfid        | kgsui843itu459gufd  |
            | floor       | 1                   |


    @rooms @unblocking @update @admin
    Scenario: unblocking a room
        Given I am an authenticated "behatUser"
        When I want to get id of "/buildings/rooms"
            | key   | value               |
            | name  | B.behat room update |
            | floor | 1                   |
        And I want to unblock the room
        Then room should be blocked "0"


    @reservations @create @anotherBehatUser
    Scenario: making a reservation
        Given I am an authenticated "anotherBehatUser"
        When I want to get id of "/buildings"
            | key  | value                 |
            | name | Behat Building update |
        And I want to get id of "/buildings/rooms"
            | key  | value               |
            | name | B.behat room update |
            | rfid | kgsui843itu459gufd  |
        And I want to make a reservation
            | key         | value                                                           |
            | title       | behat reservation                                               |
            | description | this is a behat test reservations. It will be deleted after all |
            | start_time  | 10:00:00                                                        |
            | end_time    | 11:15:00                                                        |
            | date        | 2020-12-28                                                      |
        Then data should exist in "/reservations" list
            | key         | value                                                           |
            | title       | behat reservation                                               |
            | description | this is a behat test reservations. It will be deleted after all |
            | start_time  | 10:00:00                                                        |
            | end_time    | 11:15:00                                                        |
            | date        | 2020-12-28                                                      |


    @reservation @update @behatUser
    Scenario: updating reservation
        Given I am an authenticated "behatUser"
        When I want to get id of "/reservations"
            | key         | value                                                           |
            | title       | behat reservation                                               |
            | description | this is a behat test reservations. It will be deleted after all |
            | start_time  | 10:00:00                                                        |
            | end_time    | 11:15:00                                                        |
            | date        | 2020-12-28                                                      |
        And I want to update "/reservations/" id data
            | key         | value                                                                 |
            | start_time  | 09:00:00                                                              |
            | end_time    | 12:15:00                                                              |
            | description | this is updated behat test reservations. It will be deleted after all |
        Then "/reservations/" id should have data
            | key         | value                                                                 |
            | title       | behat reservation                                                     |
            | date        | 2020-12-28                                                            |
            | start_time  | 09:00:00                                                              |
            | end_time    | 12:15:00                                                              |
            | description | this is updated behat test reservations. It will be deleted after all |


    @reservation @confirming @behatUser
    Scenario: confirming reservation
        Given I am an authenticated "behatUser"
        When I want to get id of "/reservations"
            | key        | value             |
            | title      | behat reservation |
            | date       | 2020-12-28        |
            | start_time | 09:00:00          |
            | end_time   | 12:15:00          |
        And I want to confirm this reservation
        Then reservation should be confirmed "1"


    @reservation @confirming @admin
    Scenario: deleting reservation
        Given I am an authenticated "behatUser"
        When I want to get id of "/reservations"
            | key         | value                                                                 |
            | date        | 2020-12-28                                                            |
            | start_time  | 09:00:00                                                              |
            | end_time    | 12:15:00                                                              |
            | description | this is updated behat test reservations. It will be deleted after all |

        And I want to delete "/reservations/" id
        Then "/reservations/" id should not exist


    @rooms @delete @behatUser
    Scenario: deleting a room
        Given I am an authenticated "behatUser"
        When I want to get id of "/buildings/rooms"
            | key         | value               |
            | name        | B.behat room update |
            | seats_count | 60                  |
            | floor       | 1                   |
        And I want to delete "/buildings/rooms/" id
        Then "/buildings/rooms/" id should not exist


    @room_type @delete @behatUser
    Scenario: deleting a room_type
        Given I am an authenticated "behatUser"
        When I want to get id of "/buildings/rooms/types"
            | key  | value                  |
            | name | behat room type update |
        And I want to delete "/buildings/rooms/types/" id
        Then "/buildings/rooms/types/" id should not exist


    @buildings @delete @behatUser
    Scenario: deleting a building
        Given I am an authenticated "behatUser"
        When I want to get id of "/buildings"
            | key  | value                 |
            | name | Behat Building update |
        And I want to delete "/buildings/" id
        Then "/buildings/" id should not exist


    @address @delete @behatUser
    Scenario: deleting an address
        Given I am an authenticated "behatUser"
        When I want to get id of "/addresses"
            | key         | value         |
            | country     | Poland update |
            | town        | Bydgoszcz     |
            | postal_code | 85-796        |
            | street      | Behata Update |
            | number      | 34b           |
        And I want to delete "/addresses/" id
        Then "/addresses/" id should not exist


    @users @delete @anotherBehatUser
    Scenario: another behat user remove his account
        Given I am an authenticated "anotherBehatUser"
        When I want to get id of "/users"
            | key   | value             |
            | email | another@gmail.com |
        And I want to delete "/users/" id
        Then "/users/" id should not exist


    @users @delete @behatUser
    Scenario: another behat user remove his account
        Given I am an authenticated "behatUser"
        When I want to get id of "/users"
            | key   | value           |
            | email | behat@gmail.com |
        And I want to delete "/users/" id
        Then "/users/" id should not exist


    @access @delete @admin
    Scenario: deleting an behat acces class
        Given I am an authenticated "admin"
        When I want to get id of "/accesses"
            | key  | value               |
            | name | behat access update |
        And I want to delete "/accesses/" id
        Then "/accesses/" id should not exist


