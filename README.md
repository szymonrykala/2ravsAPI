# 2ravsAPI

Room Reservation and Visualisation System API

## Changelog
[09-09-2020]
- cathing integrity error in db interface [23000 state]
- acces Controller ready
- controllers inheritance
- Controller::deleted() method true when delete is 'true' or '1' 

[27-08-2020]
- getting deleted reservations by ?deleted=true 
- on hard delete reservation - logs are deleted

[25-08-2020]
- user controller finished
- checking same user in auth middleware 

[24-08-2020]
-   change endpoint /activate --> /users/activate
-   Added ActivationException - 3007
-   done activation

## API endpoints

1. Open:

    - POST /auth    👌 <--user authorization
    - GET /users/activate   👌 <-- account activation
    - POST /users   👌 <-- user registration

2. Closed:

    - GET /logs
    - GET /logs/search
    - DELETE /logs/{id}

    - GET /addresses
    - POST /addresses

    - GET /addresses/{id}
    - PATCH /addresses/{id}
    - DELETE /addresses/{id}

    - GET /acces    👌
    - POST /acces    👌

    - GET /acces/{id}    👌
    - PATCH /acces/{id}    👌
    - DELETE /acces/{id}    👌

    - GET /reservations     👌
    - GET /reservations/{id}    👌
    - POST /reservations    👌
    - PATCH /reservations/{id}    👌
    - PATCH /reservations/{id}/confirm
    - DELETE /reservations/{id}     👌bug

    - GET reservations/search

    - GET /users    👌
    - GET /users/{id}   👌
    - PATCH /users/{id}     👌
    - DELETE /users/{id}    👌
    - GET /users/{id}/reservations  👌

    - GET /buildings
    - POST /buildings
    - GET /buildings/search

    - GET /buildings/{id}
    - PATCH /buildings/{id}
    - DELETE /buildings/{id}
    - GET /buildings/{id}/reservations      👌

    - GET /buildings/{id}/rooms
    - POST /buildings/{id}/rooms

    - GET /buildings/{id}/rooms/{idp}
    - GET /buildings/{id}/rooms/{idp}/reservations      👌
    - PATCH /buildings/{id}/rooms/{idp}
    - DELETE /buildings/{id}/rooms/{idp}
