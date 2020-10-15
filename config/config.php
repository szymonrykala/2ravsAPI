<?php
//DB
define('DSN', "mysql:host=localhost;dbname=ravs;charset=utf8mb4");
define('DB_USER', 'root');
define('DB_PASS', '');

define('SENDER_MAIL','noreply@maciejkossowski.pl');
define('DEFAULT_ACCESS',1);

define('JWT_SIGNATURE', 'r@f@#dog#l435eks#kej4$*%$ci%w5fg5g4ghf^i^3456&o7zdgdfciesko');

date_default_timezone_set("Europe/Warsaw");

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../utils/DBInterface.php';

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

require_once __DIR__ . '/../utils/MailSender.php';

require_once __DIR__ . '/../controllers/BuildingController.php';
require_once __DIR__ . '/../controllers/ReservationController.php';
require_once __DIR__ . '/../controllers/AccessController.php';
require_once __DIR__ . '/../controllers/LogController.php';
require_once __DIR__ . '/../controllers/RoomController.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/AddressController.php';
require_once __DIR__ . '/../controllers/RoomTypeController.php';

require_once __DIR__ . '/../utils/Validator.php';

function myErrorHandler(Throwable $e)
{
    $data = [
        'error' => [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            // 'trace' => $e->getTrace(),
            'code' => $e->getCode()
        ]
    ];
    http_response_code($e->getCode());
    header('content-type:application/json');
    echo json_encode($data);
    return true;
}
set_exception_handler("myErrorHandler");
