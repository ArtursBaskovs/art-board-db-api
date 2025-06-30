<?php

namespace App;

use Throwable;

class ErrorHandler
{
    public static function handleException(Throwable $exception)
    {
        http_response_code(500);
        echo json_encode([
            "Code" => $exception->getCode(),
            "Message" => $exception->getMessage(),
            "File" => $exception->getFile(),
            "Line" => $exception->getLine()
        ], JSON_PRETTY_PRINT);
    }
}
