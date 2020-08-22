# 2ravsAPI
Room Reservation and Visualisation System API

## Changelog
* not yet

## API endpoints
1. Open:
    * POST /auth
    * GET /activate
    * POST /users

2. Closed:
    * GET /logs
    * GET /logs/search
    * DELETE /logs/{id}

    * GET /addresses
    * POST /addresses

    * GET /addresses/{id}
    * PATCH /addresses/{id}
    * DELETE /addresses/{id}

    * GET /acces
    * POST /acces

    * GET /acces/{id}
    * PATCH /acces/{id}
    * DELETE /acces/{id}

    * GET /reservations
    * POST /reservations
    * GET /reservations/{id}
    * PATCH /reservations/{id}
    * DELETE /reservations/{id}

    * GET reservations/search

    * GET /users

    * GET /users/{id}
    * PATCH /users/{id}
    * DELETE /users/{id}
    * GET /users/{id}/reservations

    * GET /buildings
    * POST /buildings
    * GET /buildings/search

    * GET /buildings/{id}
    * PATCH /buildings/{id}
    * DELETE /buildings/{id}
    * GET /buildings/{id}/reservations

    * GET /buildings/{id}/rooms
    * POST /buildings/{id}/rooms

    * GET /buildings/{id}/rooms/{idp}
    * PATCH /buildings/{id}/rooms/{idp}
    * DELETE /buildings/{id}/rooms/{idp}