<?php

namespace App;

class JsonResponseHandler
{
    private array $responseArray = [];

    public function addResponse(string $messageType, mixed $message)
    {
        $allowedMessageTypes = ["Message", "Data", "Authorization"];
        if (!in_array($messageType, $allowedMessageTypes, true)) {
            $this->responseArray["Error"] = "Invalid message type: " . $messageType . ". Allowed types: " . implode(", ", $allowedMessageTypes);
            return;
        }
        $this->responseArray[$messageType] = $message;
    }

    public function getResponseJson()
    {
        echo json_encode($this->responseArray, JSON_PRETTY_PRINT);
        exit;
    }
}
