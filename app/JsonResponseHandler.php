<?php

namespace App;

class JsonResponseHandler
{
    private array $responseArray = [];

    public function addResponse(string $messageType, mixed $message)
    {
        $this->responseArray[$messageType] = $message;
    }

    public function getResponseJson()
    {
        echo json_encode($this->responseArray, JSON_PRETTY_PRINT);
        exit;
    }
}
