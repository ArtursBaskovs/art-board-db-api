# Art Board DB API

RESTful API for the Art Board project — stores board data, notes, and images in a MySQL database.

## Stack

- PHP (vanilla)
- Composer
- MySQL
- RESTful architecture

## How to run locally

- Clone repo
- Run "composer install" in bash
- Start PHP server
- Setup MySQL database (sql file is in repo)
- Setup connection info in .env file

## API Endpoints
GET: /board/{id}
POST: json file via file_get_contents("php://input")
PATCH: json file via file_get_contents("php://input")
DELETE: /board/{id}

## env file template
DATABASE_HOST=*******
DATABASE_PORT=*******
DATABASE_NAME=*******
DATABASE_USER=*******
DATABASE_PASSWORD=*******

API_KEY=*******
