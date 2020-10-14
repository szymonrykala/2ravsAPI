# 2ravsAPI

Room Reservation and Visualisation System API

## Changelog
[14.10.2020]
### Added
 - .hatacces
 - sending emails to new users with activation code

[06-10-2020]
### Added
 - Validator::class utility
 - Validation in Controllers

[05-10-2020]
### Added
 - searching utilities in Model and Controller abstract classes
 - Controller:switchKey(array &$array,string $oldKeym, string $newKey)
### Changed
 - switching keys ex. 'log_id' -> 'id' with Controller:switchKey() func.
 - controlling types and variables geted form body in Controller:getFrom
### Removed 
 - searching paths in router 
 - Model.*:search()


[04-10-2020]

### Added

-   Operations on results Sorting (sort, sort_key):
    -   limiting (limit)
    -   paging (page, on_page)
-   Controller: parsedQueryString(Request $request, string $key=null):array

### Changed

-   way to get resources parameters from URI
-   now one method menage diffrent read paths in controllers

### Removed

-   methods from controllers - one method to menage diffrent read paths
-   Controller:deleted(Request \$request):bool

[22-09-2020]

### Changed

-   displaying data in errors with json_encode()
-   Exception fix in User::verify

[17-09-2020]

### Added

-   filtering unexpected variables in model layer
-   protected column property in models
-   refactor Model::exist(array $params,bool $reverse) reverse field reverse working of function if true throws when already exist

[13-09-2020]

-   added /buildings/rooms/types get,post,patch,delete and get /buildings/rooms
-   implemented RoomController
-   added roomTypeController
-   implemented ToomTypeController

[11-09-2020]

-   BuildingsController ready
-   addressController ready
-   fixed auth middleware

[09-09-2020]

-   cathing integrity error in db interface [23000 state]
-   accesController ready
-   controllers inheritance
-   Controller::deleted() method true when delete is 'true' or '1'

[27-08-2020]

-   getting deleted reservations by ?deleted=true
-   on hard delete reservation - logs are deleted

[25-08-2020]

-   user controller finished
-   checking same user in auth middleware

[24-08-2020]

-   change endpoint /activate --> /users/activate
-   Added ActivationException - 3007
-   done activation

## API endpoints

1. Open:

    - POST /auth 👌 <--user authorization
    - GET /users/activate 👌 <-- account activation
    - POST /users 👌 <-- user registration

2. Closed:

    - GET /logs 👌
    - GET /logs/search 👌
    - DELETE /logs/{id} 👌

    - GET /addresses 👌
    - POST /addresses 👌

    - GET /addresses/{id} 👌
    - PATCH /addresses/{id} 👌
    - DELETE /addresses/{id} 👌

    - GET /acces 👌
    - POST /acces 👌

    - GET /acces/{id} 👌
    - PATCH /acces/{id} 👌
    - DELETE /acces/{id} 👌

    - GET /reservations 👌
    - GET /reservations/{id} 👌
    - POST /reservations 👌
    - PATCH /reservations/{id} 👌
    - PATCH /reservations/{id}/confirm
    - DELETE /reservations/{id} 👌bug

    - GET reservations/search

    - GET /users 👌
    - GET /users/{id} 👌
    - PATCH /users/{id} 👌
    - DELETE /users/{id} 👌
    - GET /users/{id}/reservations 👌

    - GET /buildings 👌
    - POST /buildings 👌
    - GET /buildings/search 👌

    - GET /buildings/{id} 👌
    - PATCH /buildings/{id} 👌
    - DELETE /buildings/{id} 👌
    - GET /buildings/{id}/reservations 👌

    - GET /buildings/rooms/types 👌
    - POST /buildings/rooms/types 👌
    - PATCH /buildings/rooms/types 👌
    - DELETE /buildings/rooms/types 👌

    - GET /buildings/rooms 👌

    - GET /buildings/{id}/rooms 👌
    - POST /buildings/{id}/rooms 👌

    - GET /buildings/{id}/rooms/{idp} 👌
    - GET /buildings/{id}/rooms/{idp}/reservations 👌
    - PATCH /buildings/{id}/rooms/{idp} 👌
    - DELETE /buildings/{id}/rooms/{idp} 👌
