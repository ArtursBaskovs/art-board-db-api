<?php

namespace App;

use PDO;
use PDOException;
use Dotenv\Dotenv;
use Throwable;

class DBconnection
{
    public function connect()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();


        try {
            $dbh = new PDO(
                'mysql:host=' . $_ENV['DATABASE_HOST'] . ';dbname=' . $_ENV['DATABASE_NAME'],
                $_ENV['DATABASE_USER'],
                $_ENV['DATABASE_PASSWORD']
            );
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbh->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);

            return $dbh;
        } catch (Throwable $e) {
            ErrorHandler::handleException($e);
            exit;
        }

        return $dbh;
    }
}




//https://gist.github.com/mdang/bac2f5d7db8f305603219e6084f0d93b