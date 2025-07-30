<?php

namespace App;

use App\JsonResponseHandler;
use App\APIkeyAuth;

class BoardController
{
    private ?array $partsOfUrl;
    private ?string $resource;
    private ?string $boardId;
    //collects all messages in controller and then echoes it to a client side
    private JsonResponseHandler $respond;
    private APIkeyAuth $checkKeyAuth;

    public function __construct(private BoardGateway $gateway, ?array $partsOfUrl = null)
    {
        $this->partsOfUrl = $partsOfUrl;
        $this->resource = $partsOfUrl[0] ?? null;
        $this->boardId = $partsOfUrl[1] ?? null;
        $this->respond = new JsonResponseHandler;
        $this->checkKeyAuth = new APIkeyAuth($this->respond);
    }

    public function processRequest(string $requestMethod): void
    {
        if (!$this->checkKeyAuth->checkAPIKey()) {
            http_response_code(401);
            $this->respond->getResponseJson();
            return;
        }
        //var_dump($this->resource);

        if ($requestMethod == "POST") $this->processPOST();

        if ($requestMethod == "GET") $this->processGET();

        if ($requestMethod == "PATCH") $this->processPATCH();

        if ($requestMethod == "DELETE") $this->processDELETE();

        if ($requestMethod != null) $this->respond->getResponseJson();
    }
    private function processPOST()
    {
        $data = file_get_contents("php://input");
        if ($data == null) {
            http_response_code(422);
            $this->respond->addResponse("Message", "Recieved null. JSON data is required.");
            $this->respond->addResponse("Data", null);
            return;
        }

        $jsonData = json_decode($data, true);
        //var_dump($jsonData);
        $newBoardID = $this->gateway->insertData($jsonData);

        http_response_code(201);
        $this->respond->addResponse("Message", "New board was saved in database");
        $this->respond->addResponse("Data", $newBoardID);
        //echo "New board was saved in database ";
        //echo $newBoardID;
    }
    private function processGET()
    {
        $id = $this->boardId;
        $doesBoardExist = $this->boardIDExists();
        if ($doesBoardExist) {
            $result = $this->gateway->getBoardDataByID($id);
            //$jsonData = json_encode($result, JSON_PRETTY_PRINT);
            //echo $jsonData;
            $this->respond->addResponse("Message", "Acquired data for board: " . $id);
            $this->respond->addResponse("Data", $result);
        }
    }
    private function processPATCH()
    {
        $id = $this->boardId;
        $doesBoardExist = $this->boardIDExists();
        if (!$doesBoardExist) return;

        $data = file_get_contents("php://input");
        if ($data == null) {
            http_response_code(422);
            $this->respond->addResponse("Message", "Recieved null. JSON data is required.");
            $this->respond->addResponse("Data", $id);
            return;
        }

        $jsonData = json_decode($data, true);
        $this->gateway->updateData($jsonData, $id);
        $this->respond->addResponse("Message", "Updated data for board with ID: " . $id);
        $this->respond->addResponse("Data", $id);
    }
    private function processDELETE()
    {
        $id = $this->boardId;
        $doesBoardExist = $this->boardIDExists();
        if (!$doesBoardExist) return;

        $this->gateway->deleteBoard($id);
        $this->respond->addResponse("Message", "Deleted board: " . $id);
        $this->respond->addResponse("Data", $id);
    }


    private function boardIDexists(): bool
    {
        $boardIdExists = false;
        //check existance in db only if id was even passed
        if ($this->boardId) {
            $boardIdExists = $this->gateway->doesValueExist('boards', 'id', $this->boardId);
        } else {
            http_response_code(422);
            $this->respond->addResponse("Message", "No board ID was passed in this request");
            $this->respond->addResponse("Data", $this->boardId);
            return false;
        }
        if ($boardIdExists) {
            return true;
        } else {
            http_response_code(404);
            $this->respond->addResponse("Message", "No board found with this id: " . $this->boardId);
            $this->respond->addResponse("Data", $this->boardId);
            return false;
            exit;
        }
    }
}
