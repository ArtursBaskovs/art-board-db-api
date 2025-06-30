<?php

namespace App;


class BoardController
{
    private ?array $partsOfUrl;
    private ?string $resource;
    private ?string $boardId;

    public function __construct(private BoardGateway $gateway, ?array $partsOfUrl = null)
    {
        $this->partsOfUrl = $partsOfUrl;
        $this->resource = $partsOfUrl[0] ?? null;
        $this->boardId = $partsOfUrl[1] ?? null;
    }

    public function processRequest(string $requestMethod): void
    {
        $this->processDataRequest($requestMethod);
    }

    private function processDataRequest(string $method): void
    {
        $boardIdExists = false;
        if ($this->boardId) {
            $boardIdExists = $this->gateway->doesValueExist('boards', 'id', $this->boardId);
        } else {
            var_dump("no board found with this id");
        }
        if ($method == "POST") {
            $data = file_get_contents("php://input");
            $jsonData = json_decode($data, true);
            //var_dump($jsonData);
            $this->gateway->insertData($jsonData);
        }
        if ($method == "GET" && $boardIdExists) {
            var_dump("this board exists");
            $this->gateway->getBoardDataByID($this->boardId);
        }
        if ($method == "PATCH" && $boardIdExists) {
            var_dump("This board exists. Updating now");
            $data = file_get_contents("php://input");
            $jsonData = json_decode($data, true);

            $this->gateway->updateData($jsonData, $this->boardId);
        }
        if ($method == "DELETE" && $boardIdExists) {
            $this->gateway->deleteBoard($this->boardId);
        }
    }
}
