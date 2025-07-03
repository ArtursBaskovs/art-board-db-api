<?php

namespace App;

use App\ErrorHandler;
use Throwable;

class APIkeyAuth
{
    private string $validKey;

    public function __construct(private JsonResponseHandler $response)
    {
        $this->validKey = $_ENV['API_KEY'] ?? 'default';
        //$this->response = new JsonResponseHandler;
    }

    public function checkAPIKey(): bool
    {
        try {
            $headers = getallheaders();
            //get header contents to check if token type and api key is correct
            $auth = $headers['Authorization'] ?? '';
            $authParts = explode(' ', $auth);
            $tokenType = $authParts[0] ?? '';
            $key = $authParts[1] ?? '';

            if ($tokenType != "Bearer") {
                $this->response->addResponse("Authorization", "Wrong token type, bearer required.");
                return false;
            }
            if (!hash_equals($this->validKey, $key)) {
                $this->response->addResponse("Authorization", "Invalid token");
                return false;
            }

            //var_dump($authParts, $this->validKey);

            return true;
        } catch (Throwable $e) {
            ErrorHandler::handleException($e);
            exit;
        }
    }
}
