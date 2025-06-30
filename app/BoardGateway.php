<?php

namespace App;

use PDO;
use PDOException;
use Throwable;
use InvalidArgumentException;

class BoardGateway
{
    private PDO $connection;
    public function __construct(DBconnection $dbCon)
    {
        $this->connection = $dbCon->connect();
    }
    //inserts new board with all related data
    public function insertData(array $data)
    {
        //var_dump($data);
        $boardName = $data['boardName'];
        $notesArray = $data['notes'];
        $imagesArray = $data['images'];
        //var_dump($boardName);
        //inserts new board record and returns its id to link it to other 2 tables later
        $boardID = $this->insertBoard($boardName);
        $this->insertBoardElements('notes', $boardID, $notesArray);
        $this->insertBoardElements('images', $boardID, $imagesArray);

        //send it to front end for user to know and redirect to created board
        $this->getlastSavedBoardLink($boardID);

        //  Also i need to check somehow if board is new or user saves existing, 
        //meaning that i just need to rewrite existing board with same link
    }
    //updates whole board data
    public function updateData(array $data, string $boardID)
    {
        $notesArray = $data['notes'];
        $imagesArray = $data['images'];

        $this->updateBoard($data, $boardID);
        //board elements like notes and images are changing very often by user unlike board info. 
        // So I guess I can just delete and insert again those rows
        $this->deleteBoardElements($boardID, 'notes');
        $this->deleteBoardElements($boardID, 'images');
        //now inserting back with new data

        $this->insertBoardElements('notes', $boardID, $notesArray);
        $this->insertBoardElements('images', $boardID, $imagesArray);

        echo "Updated data for board: " . $boardID;
        exit;
    }
    public function deleteBoard(string $boardID)
    {
        $sql = "DELETE FROM boards WHERE id = :boardID";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':boardID', $boardID);
        $stmt->execute();
        echo "Deleted board: " . $boardID;
        exit;
    }
    private function deleteBoardElements(string $boardID, string $elementType)
    {
        $sql = match ($elementType) {
            'notes' => "DELETE FROM notes WHERE board_id = :boardID",
            'images' => "DELETE FROM images WHERE board_id = :boardID",
            default => throw new InvalidArgumentException("Unknown element type: $elementType")
        };
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':boardID', $boardID);
        $stmt->execute();
    }
    private function updateBoard(array $data, string $boardID)
    {
        //for now I can just let update a board name in case it is needed
        $sql = "UPDATE boards SET boardName = :boardName WHERE id = :boardID";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':boardID', $boardID);
        $stmt->bindParam(':boardName', $data['boardName']);
        $stmt->execute();
    }

    public function getBoardDataByID(string $boardID)
    {
        $stmt = $this->connection->prepare("SELECT * FROM boards WHERE id = :boardID");
        $stmt->execute([':boardID' => $boardID]);

        $boardData = $stmt->fetch(PDO::FETCH_ASSOC);
        $notesData = $this->getBoardsElements($boardID, 'notes');
        $imagesData = $this->getBoardsElements($boardID, 'images');

        $result = [
            'boardID' => $boardID,
            'boardData' => $this->jsonFormatBoardData($boardData, $notesData, $imagesData)
        ];

        echo json_encode($result, JSON_PRETTY_PRINT);
        exit;
    }

    private function jsonFormatBoardData(array $boardData, array $notesData, array $imagesData)
    {
        $boardName = $boardData['boardName'];
        $notesArray = [];
        foreach ($notesData as $note) {
            $notesArray[$note['id']] = [
                'id' => $note['id'],
                'className' => $note['className'],
                'value' => $note['value'],
                'posX' => $note['posX'],
                'posY' => $note['posY'],
                'height' => $note['height'],
                'width' => $note['width'],
                'link' => $note['link'],
                'type' => $note['type']
            ];
        }

        $imagesArray = [];
        foreach ($imagesData as $image) {
            $imagesArray[$image['id']] = [
                'id' => $image['id'],
                'className' => $image['className'],
                'value' => $image['value'],
                'posX' => $image['posX'],
                'posY' => $image['posY'],
                'height' => $image['height'],
                'width' => $image['width'],
                'link' => $image['link'],
                'type' => $image['type']
            ];
        }
        $wholeBoardData = [
            'boardName' => $boardName,
            'notes' => $notesArray,
            'images' => $imagesArray
        ];
        return $wholeBoardData;
    }

    private function getBoardsElements(string $boardID, string $elementType)
    {
        $sql = match ($elementType) {
            'notes' => "SELECT * FROM notes WHERE board_id = :boardID",
            'images' => "SELECT * FROM images WHERE board_id = :boardID",
            default => throw new InvalidArgumentException("Unknown element type: $elementType")
        };

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':boardID' => $boardID]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    private function insertBoard(string $boardName): string
    {
        try {
            //generating unique code for url to access this boards data from front-end
            $urlCode = $this->generateBoardUniqueLink('boards', 'id');
            //inserting board info
            $sql = "
                INSERT INTO boards (id, boardName) VALUES
                (:id, :name)
            ";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindParam(':id', $urlCode);
            $stmt->bindParam(':name', $boardName);
            $stmt->execute();

            //$lastId = $this->connection->lastInsertId();
            return $urlCode;
        } catch (Throwable $error) {
            ErrorHandler::handleException($error);
            return 0;
        }
    }

    private function insertBoardElements(string $elementType, string $boardID, array $data)
    {
        //var_dump($data);
        try {
            //I have separate tables for those elements, but their columns are the same
            $sql = $this->getSqlByElementType($elementType);

            $stmt = $this->connection->prepare($sql);
            //$stmt->bindParam(':className');
            foreach ($data as $element) {
                $stmt->bindValue(':id', $element['id']);
                $stmt->bindValue(':className', $element['className']);
                $stmt->bindValue(':value', $element['value']);
                $stmt->bindValue(':posX', $element['posX']);
                $stmt->bindValue(':posY', $element['posY']);
                $stmt->bindValue(':height', $element['height']);
                $stmt->bindValue(':width', $element['width']);
                $stmt->bindValue(':link', $element['link']);
                $stmt->bindValue(':type', $element['type']);
                $stmt->bindValue(':board_id', $boardID);
                $stmt->execute();
            }
        } catch (Throwable $error) {
            ErrorHandler::handleException($error);
            return 0;
        }
    }
    //this kind of functions maybe later I will add to separate class
    private function getSqlByElementType(string $elementType): string
    {
        $sql = "";
        if ($elementType == 'notes') {
            $sql = "
                INSERT INTO notes (id, className, value, posX, posY, height, width, link, type, board_id) VALUES
                (:id, :className, :value, :posX, :posY, :height, :width, :link, :type, :board_id)
            ";
        }
        if ($elementType == 'images') {
            $sql = "
                INSERT INTO images (id, className, value, posX, posY, height, width, link, type, board_id) VALUES
                (:id, :className, :value, :posX, :posY, :height, :width, :link, :type, :board_id)
            ";
        }
        return $sql;
    }
    private function generateBoardUniqueLink(string $column, string $value): string
    {
        $urlCode = "";
        do {
            $urlCode = bin2hex(random_bytes(4));
            $doesCodeExist = $this->doesValueExist('boards', 'id', $urlCode);
            var_dump("generating unique url code");
        } while ($doesCodeExist == true);
        return $urlCode;
    }
    public function doesValueExist(string $table, string $column, string $value): bool
    {
        $stmt = $this->connection->prepare("SELECT 1 FROM $table WHERE $column = :value");
        $stmt->execute([':value' => $value]);
        $result = $stmt->fetchColumn();
        if ($result) return true;
        return false;
    }
    private function getlastSavedBoardLink(string $boardID) {}
    private function recordByNameExists() {}
}
