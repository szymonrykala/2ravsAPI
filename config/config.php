<?php
//DB
define('DSN', "mysql:host=localhost;dbname=ravs;charset=utf8mb4");
define('DB_USER', 'root');
define('DB_PASS', '');

// URL path
define('ROOT', 'http://localhost:8080');

// //tables in DB
// define('USER_BASE', 'user');
// define('RESERVATION_BASE', 'reservation');
// define('BUILDING_BASE', 'building');
// define('ROOM_BASE', 'room');
// define('ACCES_BASE', 'access');
// define('LOG_BASE', 'log');
// define('ADDRESS_BASE', 'address');
// define('ROOM_TYPE_BASE', 'room_type');

define('JWT_SIGNATURE', 'r@f@#dog#l435eks#kej4$*%$ci%w5fg5g4ghf^i^3456&o7zdgdfciesko');

date_default_timezone_set("Europe/Warsaw");

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../interfaces/DBInterface.php';
require_once __DIR__ . '/../interfaces/ViewInterface.php';

require_once __DIR__ . '/../middleware/AuthorizationMiddleware.php';
require_once __DIR__ . '/../middleware/JWTMiddleware.php';
require_once __DIR__ . '/../middleware/JSONMiddleware.php';


require_once __DIR__ . '/../models/Building.php';
require_once __DIR__ . '/../models/Access.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/../models/Reservation.php';
require_once __DIR__ . '/../models/Room.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Model.php';
require_once __DIR__ . '/../models/Address.php';
require_once __DIR__ . '/../models/RoomType.php';

// require_once __DIR__ . '/../mail/Mail.php';

require_once __DIR__ . '/../controllers/BuildingController.php';
require_once __DIR__ . '/../controllers/ReservationController.php';
require_once __DIR__ . '/../controllers/AccessController.php';
require_once __DIR__ . '/../controllers/LogController.php';
require_once __DIR__ . '/../controllers/RoomController.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/AddressController.php';
require_once __DIR__ . '/../controllers/RoomTypeController.php';

/*
require_once __DIR__ . '/../exceptions/NothingFoundException.php';
require_once __DIR__ . '/../exceptions/UnUpdetableParameterException.php';
require_once __DIR__ . '/../exceptions/EmptyVariableException.php';
require_once __DIR__ . '/../exceptions/AlreadyExistException.php';
require_once __DIR__ . '/../exceptions/ReservationException.php';
require_once __DIR__ . '/../exceptions/NotExistException.php';
require_once __DIR__ . '/../exceptions/IncorrectRequestBodyException.php';
require_once __DIR__ . '/../exceptions/RequiredParameterException.php';
require_once __DIR__ . '/../exceptions/AuthenticationException.php';
require_once __DIR__ . '/../exceptions/AuthenticationFailsCountException.php';
require_once __DIR__ . '/../exceptions/CredentialsPolicyException.php';
require_once __DIR__ . '/../exceptions/AuthorizationException.php';
require_once __DIR__ . '/../exceptions/ActivationException.php';
require_once __DIR__ . '/../exceptions/ReservationLockException.php';
require_once __DIR__ . '/../exceptions/APIException.php';

*/


