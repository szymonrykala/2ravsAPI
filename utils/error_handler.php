<?php

return function (Throwable $e) {
    $respCode = $e->getCode() == 0 ? 500 : $e -> getCode();
    http_response_code($respCode);
    header('content-type:application/json');
    echo json_encode([
        'error' => [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            // 'trace' => $e->getTrace(),
            'code' => $respCode
        ]
    ]);
    return true;
};
