<?php

return function (Throwable $e) {
    http_response_code($e->getCode());
    header('content-type:application/json');
    echo json_encode([
        'error' => [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            // 'trace' => $e->getTrace(),
            'code' => $e->getCode()
        ]
    ]);
    return true;
};
