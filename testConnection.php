<?php
require_once __DIR__ . '/../vendor/autoload.php';


use App\DBconnection;

$db = new DBconnection();
$conn = $db->connect();

if ($conn instanceof PDO) {
    echo "connection to db succesful";
}