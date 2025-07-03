<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST, PATCH, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once __DIR__ . '/vendor/autoload.php';

use App\BoardController;
use App\BoardGateway;
use App\DBconnection;

set_exception_handler(["App\\ErrorHandler", "handleException"]);

//pass request info to controller like method and what data in needed
$url = trim($_SERVER['REQUEST_URI'], "/");
$partsOfUrl = explode("/", $url);

//initated db connection for gateway and passed to controller for requests
$db = new DBconnection();
$conn = $db->connect();
$boardGateway = new BoardGateway($db);
$boardController = new BoardController($boardGateway, $partsOfUrl);

$boardController->processRequest($_SERVER['REQUEST_METHOD']);

//checking if db connection works
/*
if ($conn) {
    echo "connection to db succesful";
} else {
    echo "no connection to db :(";
}
*/