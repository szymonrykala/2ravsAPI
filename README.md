# 2ravsAPI

Reservation and Visualisation System API
##### **Use example-domain/v1 as root path to all paths in endpoints**
## Shortcuts:

1. [Features](#Features)
    - [Sorting](#Sorting)
    - [Paging](#Paging)
    - [Resource_extensions](#Resource_extensions)
    - [Search](#Search)
    - [Tokens_controll](#Tokens_controll)
2. [Responses](#Responses)
3. [Endpoints](Endpoints)
    - [RFID](#RFID)
    - [Logs](#Logs)
    - [Addresses](#Addresses)
    - [Access_classes](#Access_classes)
    - [Reservations](#Reservations)
    - [Users](#Users)
    - [Buildings](#Buildings)
    - [Room_types](#Room_types)
    - [Rooms](#Rooms)
4. [Catalog structure](#Catalog_structure)

---

## #Features

### Sorting

System umożliwia sortowanie wyników w rosnącym `ASC` i malejącym `DESC` porządku poprzez podanie wartości `sort`.
Dodatkowo istnieje możliwość wskazania klucza, według którego sortowanie jest dokonywane przez dodania zmiennej `sort_key`.

> `GET /resource?sort=ASC&sort_key=created_at` zwróci listę posortowaną według podanego klucza oraz wskazanego porządku.
> Jeśli wskazany klucz nie jest obecny w pobieranym zasobie, nie brany jest on pod uwagę i następuje sortowanie według domyślnego klucza-`id`.

### #Paging
Możliwe jest stronicowanie otrzymanych wyników poprzez podanie wartości `page` - numer strony oraz `on_page` - ilość elementów na stronie.

> `GET /resource?page=0&on_page=10` zwróci pierwszą stronę z dziesięcioma wynikami.

### #Resource_extensions

Każdy zwracany zasób posiadający `id` innego zasobu, może zostać o niego rozszerzony. Oznacza to, że jeśli pobierzemy rekord, lub klika rekordów z `rooms`. Jako wynik otrzymamy następującą odpowiedź:
`GET /buildings/2/rooms/5`
```json
{
    "items": [
        {
            "id": 5,
            "name": "A001",
            "rfid": "ytfjhyd",
            "building_id": 2,
            "room_type_id": 1,
            "seats_count": 30,
            "floor": 0,
            "equipment": "tablica,rzutnik,kreda",
            "blockade": false,
            "state": false
        }
    ]
}
```
Jak widać, powyższy wynik ma pola `building_id` oraz `room_type_id`. Mogą one zostać zastąpione zasobami na które wskazują. Aby tego dokonać, należy o które zasoby chcemy rekord rozbudować. Dokonujemy tego przed dodanie zmiennej `ext` w query string przyjmującej listę pól oddzielonych przecinkami. Tak więc `GET /buildings/2/rooms/5?ext=building_id,room_type_id` zwróci nam:
```json
{
    "items": [
        {
            "id": 5,
            "name": "A001",
            "rfid": "ytfjhyd",
            "seats_count": 30,
            "floor": 0,
            "equipment": "tablica,rzutnik,kreda",
            "blockade": false,
            "state": false,
            "room_type": {
                "id": 1,
                "name": "laboratory"
            },
            "building": {
                "id": 2,
                "name": "Budynek A",
                "rooms_count": 5,
                "address_id": 1
            }
        }
    ]
}
```
### #Search

Wszędzie gdzie używana jest metoda `GET`, możliwe jest zastosowanie trybu wyszukiwania. Zostaje on aktywowany, gdy w ciele wiadomości w formacie JSON umieścimy następującą strukturę:

```json
{
    "search": {
        "mode": "< | > | = | LIKE | REGEXP",
        "params": {
            "key": "value"
        }
    }
}
```

Zarówno pole "mode" jak i "params" są wymagane. Dostępne tryby wyszukiwania ("mode") to:

-   `<` - mniejsze od podanej wartości
-   `>` - większe od podanej wartości
-   `=` - równe podanej wartości
-   `LIKE` - działa jak `LIKE` w języku SQL
-   `REGEXP` - wyszukuje według wyrażeń regularnych obsługiwanych przez MySQL

### #Tokens_controll

By easy changing default settings for JWT authorization You can disable or enable expiration of tokens, or force all users to log in again. All settings for JWT are in file `/config/defaults.php`. Algorithm used for coding tokens is HMAC with SHA-2 - HS512

-   `signature` - Jest to podpis każdego tokenu. Poprzez jego zmianę możena zmusić wszystkich użytkowników to zalogowania się jeszcze raz.
-   `is_expire` - Definiuje czy tokeny sprawdzane są pod kontem terminu ważności - czy wygasają.
-   `valid_time` - Jeśli `is_expire=> true`, określa liczbę sekund przez ile token jest dostępny
-   `ip_controll` - Włącza kontrolę lokalnego adresu ip użytkownika. Jeśli ip ulegnie zmianie względem użytego podcas pobierania tokenu, zostanie zwrócony status `HTTP 401`

---

## #Responses:

-   Succes:
    -   OK 200
    -   Created 201
    -   Deleted 204
    -   Updated 204
-   User Errors:
    -   Bad Request 400
    -   Unauthorized 401
    -   Forbidden 403
    -   Not Found 404
    -   Method Not Allowed 405
    -   Conflict 409
-   Service Errors:
    -   Internal Server Error 500
    -   Service Not Avaliable 503

---

## #Endpoints

### #RFID

-   PATCH /rfid
    > Przełączanie stanu zajętości pokoju o danym "rfid" w ciele wiadomości
    ```json
    { "rfid": "j5jbkdg98i4u59ogfdo84" }
    ```

### #Logs:

-   GET /logs
-   GET /logs/{id}
    > Pobieranie wszystkich logów (zgodnych z funkcją search) lub konkretny log za pomocą `id`
-   DELETE /logs/{id}
    > Usuwanie wskazanych logów. Logi do usunięcia można wskazać poprzez podanie `id` loga jako parametr w URI. Kolejnym sposobem jest ustawienie `id` na wartość mniejszą od 0 (np /logs/-1) oraz ustawić następującą wiadomość jako json, gdzie podajemy listę `id` do usunięcia.
    ```json
    { "IDs": [2, 5, 7, 4] }
    ```

### #Addresses:

-   GET /addresses
-   GET /addresses/{id}
    > Pobieranie wszystkich adresów (zgodnych z funkcją search) lub konkretny adres za pomocą `id`
-   POST /addresses
    > Tworzenie nowego adresu. Wszystkie pola są wymagane.
    ```json
    {
        "country": "Poland",
        "town": "Bydgoszcz",
        "postal_code": "85-791",
        "street": "Kaliskiego",
        "number": "47"
    }
    ```
-   PATCH /addresses/{id}
    > Aktualizacja informacji adresu o podanym `id`. Wszystkie pola są opcjonalne.
    ```json
    {
        "country": "Poland",
        "town": "Bydgoszcz",
        "postal_code": "85-791",
        "street": "Kaliskiego",
        "number": "47"
    }
    ```
-   DELETE /addresses/{id}
    > Usuwanie adresu - powiedzie się jeśli nie ma do niego przypisanego żadnego budynku.
    > Jako `id` podajemy id adresu który chcemy usunąć.

### #Access_classes:

-   GET /access
-   GET /access/{id}
    > Pobieranie wszystkich klas dostępu (zgodnych z funkcją search) lub konkretną klasę za pomocą `id`
-   POST /access
    > Tworzenie nowej klasy dostępu. Wszystkie pola są wymagane.
    ```json
    {
        "name": "demo access",
        "rfid_action": false,
        "access_edit": false,
        "buildings_view": true,
        "buildings_edit": false,
        "logs_view": false,
        "logs_edit": false,
        "rooms_view": true,
        "rooms_edit": false,
        "reservations_access": false,
        "reservations_confirm": false,
        "reservations_edit": false,
        "users_edit": false,
        "statistics_view": true
    }
    ```
-   PATCH /access/{id}
    > Aktualizowania dancyh klasy dostępu o podanym `id`. Wszystkie pola są opcjonalne.
    > Po aktualizacji klasy dostępu, wszyscy użytkownicy, przypisani do tej klasy muszą zalogować się jeszcze raz.
    ```json
    {
        "name": "demo access",
        "rfid_action": false,
        "access_edit": false,
        "buildings_view": true,
        "buildings_edit": false,
        "logs_view": false,
        "logs_edit": false,
        "rooms_view": true,
        "rooms_edit": false,
        "reservations_access": false,
        "reservations_confirm": false,
        "reservations_edit": false,
        "users_edit": false,
        "statistics_view": true
    }
    ```
-   DELETE /access/{id}
    > Usuwanie klasy dostępu - powiedzie się jeśli nie ma do niego przypisanego żadnego użtykownika.
    > Jako `id` podajemy id klasy dostępu którą chcemy usunąć.

### #Reservations:

-   Pobieranie rezerwacji:

    -   GET /reservations
    -   GET /reservations/{id}
        > Pobieranie wszystkich reserwacji (zgodnych z funkcją search) lub konkretną rezerqację za pomocą `id`
    -   GET /buildings/{id}/reservations
        > Pobieranie wszystkich (zgodnych z funkcją search) rezerwacji pokoji w budynku o danym `id`.
    -   GET /buildings/{building_id}/rooms/{room_id}/reservations
        > Pobieranie wszystkich (zgodnych z funkcją search) rezerwacji pokoju o daynm `room_id` znajdującego się w budynku o danym `building_id`. Jeśli w podanym budynku nie znajduje się podany pokój, zostanie zwrócony błąd HTTP 404.
    -   GET /users/{id}/reservations
        > Pobieranie wszystkich (zgodnych z funkcją search) rezerwacji dokonanych przez użytkownika o danym `id`.

-   POST /buildings/{building_id}/rooms/{room_id}/reservations
    > Tworzenie rezerwacji. Wszystkie pola są wymagane.
    > Rezerwowany pokój wskazujemy poprzez budynek o danym `building_id` w którym znajduje się pokój o danym `room_id`
    ```json
    {
        "title": "tytuł rezerwacji",
        "subtitle": "podtytuł rezerwacji, opis",
        "start_time": "10:00",
        "end_time": "11:15",
        "date": "2020-08-28"
    }
    ```
-   PATCH /reservations/{id}
    > Aktualizacja danych rezerwacji o danym `id`. Wszystkie pola są opcjonalne.
    > Możliwe tylko wtedy, gdy rezerwacja nie jest jeszcze zatwierdzona.
    ```json
    {
        "title": "tytuł rezerwacji",
        "subtitle": "podtytuł rezerwacji, opis",
        "start_time": "10:00",
        "end_time": "11:15",
        "date": "2020-08-28"
    }
    ```
-   PATCH /reservations/{id}/confirm
    > Potwierdzenie rezerwacji.
    ```json
    { "confirmed": true }
    ```
-   DELETE /reservations/{id}
    > Usuwanie rezerwacji o danym `id`

### #Users:

-   GET /users
-   GET /users/{id}
    > Pobieranie wszystkich użytkowników (zgodnych z funkcją search) lub konkretnego użytkownika za pomocą `id`
-   POST /auth
    > Uwierzytelnianie użytkownika.
    ```json
    {
        "email": "jan.kowalski@exmail.com",
        "password": "myPassS144$"
    }
    ```
    > W odpowiedzi zwracany jest token, ID użytkownika oraz klasa dostępu. Czas ważności dostępu możliwy jest do ustawienia w pliku /`config/defaults.php` zmienna
    ```json
    {
        "items": {
            "jwt": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzM4NCJ9.eyJ1c2VyX2lkIjo4LCJhY2Nlc3NfaWQiOjMsImVtYWlsIjoid2Vyb25pa2ExMjEyQGdtYWlsLmNvbSIsImV4IjoxNjAzMTM0NzI2fQ.a5LsMCkowiD94LFalWy_RqTUwoBQPYOcLQSqfGzMktjYJxCz56xe0W8D42caP1Lq",
            "userID": 15,
            "access": [
                {
                    "id": 5,
                    "name": "example access",
                    "rfid_action": true,
                    "access_edit": false,
                    "buildings_view": true,
                    "buildings_edit": true,
                    "logs_view": true,
                    "logs_edit": false,
                    "rooms_view": true,
                    "rooms_edit": true,
                    "reservations_access": true,
                    "reservations_confirm": false,
                    "reservations_edit": true,
                    "users_edit": false,
                    "statistics_view": false
                }
            ]
        }
    }
    ```
-   POST /users
    > Rejestracja nowego użytkownika. Wszystkie pola są wymagane.
    > Domyślnie przypisywana klasa dostępu zdefiniowana jest jako `DEFAULT_ACCESS` w pliku config.php.
    ```json
    {
        "name": "Jan",
        "surname": "Kowalski",
        "email": "jan.kowalski@exmail.com",
        "password": "myPassS144$",
        "repeat_password": "myPassS144$"
    }
    ```
-   PATCH /users/action
    > Akcja z użyciem kodu otrzymanego na maila. Pola `email` oraz `password` są wymagane w celach uwierzytelniania.
    > Pole `action` może przyjmować trzy wartości:
    -   `resend` - prośba o ponowne wysłanie maila z kodem aktywacyjnym
    -   `activate` - aktywacja użytkownika
    -   `change_email` - zmiana emaila użytkownika. Pole `email` wówczas musi zawierać nowy email
    ```json
    {
        "email": "my.email@exemail.com",
        "password": "myPassS144$",
        "key": "9t85v",
        "action": "resend | activate | change_email"
    }
    ```
-   PATCH /users/{id}
    > Aktualizacja danych użytkownika. Pola `old_password` i `new_password` są wymagane tylko w przypadku, gdy użytkownik chce zmienić hasło. Aktualizacja klasy kodstępu użytkownika przez pole `access_id` jest możliwa tylko przez użytkownika mającego zezwalającą na to klasę dostępu. Reszta pól jest opcjonalna.
    > Chcąc zmienić email, wpisujemy w pole `email` nową wartość. Na nowy adres email zostaje wysłany mail z kodem aktywacyjnym. Kod aktywacyjny potrzebny należy wykorzystać w `PATCH /users/action`
    ```json
    {
        "name": "Jan",
        "surname": "Kowalski",
        "email": "jan.kowalsky@exmail.com",
        "old_password": "oldpass",
        "new_password": "newpass",
        "access_id": 4
    }
    ```
-   DELETE /users/{id}
    > Usuwanie użytkownika o danym `id`. Dokonać może tego albo usuwany użytkownik, albo użytkownik do tego upoważniony przez odpowiednią klasę dostępu.
    > Usunięcie użytkownika spowoduje usunięcie wszystkich jego rezerwacji.

### #Buildings:

-   GET /buildings
-   GET /buildings/{id}
    > Pobieranie wszystkich budynków (zgodnych z funkcją search) lub konkretnego budynku za pomocą `id`
-   POST /buildings
    > Tworzenie nowego budynku. Wszystkie pola są wymagane.
    ```json
    {
        "name": "example name",
        "address_id": 2
    }
    ```
-   PATCH /buildings/{id}
    > Aktualizowania danych budynku o podanym `id`. Wszystkie pola są opcjonalne.
    ```json
    {
        "name": "example name",
        "rooms_count": 20,
        "address_id": 2
    }
    ```
-   DELETE /buildings/{id}
    > Jako `id` podajemy id budynku który chcemy usunąć - powiedzie się jęsli nie ma w nim pokoji.
    > Usuwanie budynku; spowoduje:
    -   usunięcie rezerwacji pokoji w tym budynku

### #Room_types:

-   GET /rooms/types
    > Pobieranie wszystkich typów pokoji (zgodnych z funkcją search).
-   POST /rooms/types
    > Tworzenie nowego typu pokoji. Pole jest wymagane.
    ```json
    {
        "name": "laboratorium"
    }
    ```
-   PATCH /rooms/types
    > Aktualizacja danych typu. Pole jest wymagane.
    ```json
    {
        "name": "laboratorium"
    }
    ```
-   DELETE /rooms/types
    > Usunięcie typu - powiedzie się, jeśli nie ma pokoji z takim typem.

### #Rooms:

-   Pobieranie pokoji:
    > Pobieranie pokoji lub pokoju o danym `id` bez względu na budynek.
    -   GET /rooms
    -   GET /rooms/{id}
        > Pobieranie pokoji lub pokoju o danym `room_id`.
    -   GET /buildings/{building_id}/rooms
    -   GET /buildings/{building_id}/rooms/{id}
        > Pobieranie pokoji lub pokoju o danym `room_id` znajdującym się w danym budynku o danym `building_id`.
-   POST /buildings/{id}/rooms
    > Tworzenie nowego pokoju wewnątrz budynku o danym `id`. Wszystkie pola są wymagane.
    ```json
    {
        "name": "B.001",
        "rfid": "sdafgw435tgwtr",
        "room_type_id": 3,
        "seats_count": 60,
        "floor": 1,
        "equipment": "umywalka,kreda,tablica"
    }
    ```
-   PATCH /buildings/rooms/{room_id}
    > Aktualizacja danych pokoju o danym `room_id`. Gdy "blockad":true, rezerwacja pokoju jest niemożliwa.
    ```json
    {
        "name": "B.002",
        "room_type_id": 2,
        "seats_count": 70,
        "floor": 1,
        "equipment": "umywalka,kreda,tablica,30xPC",
        "blockade": true
    }
    ```
-   DELETE /buildings/rooms/{room_id}
    > Usuwanie pokoju o danym `room_id`.
    > Usunięcie spowoduje usunięcie wszystkich rezerwacji tego pokoju.

---

## #Catalog_structure

```s
   .
   +-- \_config
   |    +-- config.php
   +-- \_controllers
   |    +-- Controller.php
   |    +-- AccessController.php
   |    +-- AddressController.php
   |    +-- BuildingController.php
   |    +-- LogController.php
   |    +-- ReservationController.php
   |    +-- RoomController.php
   |    +-- RoomTypeController.php
   |    +-- UserController.php
   +-- \_middleware
   |    +-- AuthorizationMiddleware.php
   |    +-- JSONMiddleware.php
   |    +-- JWTMiddleware.php
   +-- \_models
   |    +-- Model.php
   |    +-- Access.php
   |    +-- Address.php
   |    +-- Building.php
   |    +-- Log.php
   |    +-- Reservation.php
   |    +-- Room.php
   |    +-- RoomType.php
   |    +-- User.php
   +-- \_public
   |    +-- index.php
   +-- \_utils
   |    +-- DBInterface.php
   |    +-- MailSender.php
   |    +-- Validator.php
   +-- \_vendor
   |    +-- ...
   +-- .gitignore
   +-- .htaccess
   +-- composer.json
   +-- composer.lock
   +-- dump.sql
   +-- README.md
```

---
