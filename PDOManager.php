<?php

class PDOManager {
    private string $serverName;
    private string $userName;
    private string $userPassword;
    private string $databaseName;

    public function __construct($serverName, $userName, $userPassword, $databaseName) {
        $this->serverName = $serverName;
        $this->userName = $userName;
        $this->userPassword = $userPassword;
        $this->databaseName = $databaseName;
    }

    public function findAllInventors() {
        $result = null;
        try {
            $connection = new PDO(
                "mysql:host=$this->serverName;dbname=$this->databaseName",
                $this->userName,
                $this->userPassword
            );
            $statement = $connection->prepare(
                "SELECT * FROM inventors");
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $result = $statement->fetchAll();

            $connection = null;
        } catch (PDOException $exception) {
            return null;
        }
        return $result;
    }

    public function findInventorWithInventionsById($id) {
        $result = null;
        try {
            $connection = new PDO(
                "mysql:host=$this->serverName;dbname=$this->databaseName",
                $this->userName,
                $this->userPassword
            );
            $statement = $connection->prepare(
                "SELECT * FROM inventors WHERE id = ?");
            $statement->execute([$id]);
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $result = $statement->fetch();

            $statement = $connection->prepare(
                "SELECT * FROM inventions WHERE inventor_id = ?");
            $statement->execute([$result["id"]]);
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $result["inventions"] = $statement->fetchAll();

            $connection = null;
        } catch (PDOException $exception) {
            return null;
        }
        return $result;
    }

    public function findInventorBySurname($surname) {
        $result = null;
        try {
            $connection = new PDO(
                "mysql:host=$this->serverName;dbname=$this->databaseName",
                $this->userName,
                $this->userPassword
            );
            $statement = $connection->prepare(
                "SELECT * FROM inventors WHERE surname LIKE ?");
            $statement->execute([$surname]);
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $result = $statement->fetchAll();

            $connection = null;
        } catch (PDOException $exception) {
            return null;
        }
        return $result;
    }

    public function findInventionsByCentury($century) {
        $result = null;
        try {
            $connection = new PDO(
                "mysql:host=$this->serverName;dbname=$this->databaseName",
                $this->userName,
                $this->userPassword
            );
            $statement = $connection->prepare(
                "SELECT * FROM inventions WHERE invention BETWEEN ? AND ?");
            $statement->execute([($century - 1) * 100, (($century - 1) * 100) + 99]);
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $result = $statement->fetchAll();

            $connection = null;
        } catch (PDOException $exception) {
            return null;
        }
        return $result;
    }

    public function findEventByYear($year) {
        $result = null;
        try {
            $connection = new PDO(
                "mysql:host=$this->serverName;dbname=$this->databaseName",
                $this->userName,
                $this->userPassword
            );
            $statement = $connection->prepare(
                "SELECT * FROM inventions WHERE invention = ?");
            $statement->execute([$year]);
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $result1 = $statement->fetchAll();

            $statement = $connection->prepare(
                "SELECT * FROM inventors WHERE birth = ? OR death = ?");
            $statement->execute([$year, $year]);
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $result2 = $statement->fetchAll();

            $result = array("inventions" => $result1, "inventors" => $result2);

            $connection = null;
        } catch (PDOException $exception) {
            return null;
        }
        return $result;
    }

    public function insertInventor($name, $surname, $birth, $birthplace, $death, $deathplace, $description) {
        $result = null;
        try {
            $connection = new PDO(
                "mysql:host=$this->serverName;dbname=$this->databaseName",
                $this->userName,
                $this->userPassword
            );
            $connection->beginTransaction();
            $statement = $connection->prepare(
                "INSERT INTO inventors(name, surname, birth, birthplace, death, deathplace, description) VALUES(?, ?, ?, ?, ?, ?, ?)");
            $statement->execute([$name, $surname, $birth, $birthplace, $death, $deathplace, $description]);
            $result = array("id"=>$connection->lastInsertId(), "name"=>$name, "surname"=>$surname,  "birth"=>$birth,
                "birthplace"=>$birthplace, "death"=>$death, "deathplace"=>$deathplace, "description"=>$description);
            $connection->commit();
            $connection = null;
        } catch (PDOException $exception) {
            return null;
        }
        return $result;
    }

    public function insertInvention($inventorId, $invention, $description) {
        $result = null;
        try {
            $connection = new PDO(
                "mysql:host=$this->serverName;dbname=$this->databaseName",
                $this->userName,
                $this->userPassword
            );
            $connection->beginTransaction();
            $statement = $connection->prepare(
                "INSERT INTO inventions(inventor_id, invention, description) VALUES(?, ?, ?)");
            $statement->execute([$inventorId, $invention, $description]);
            $result = array("id"=>$connection->lastInsertId(), "inventor_id"=>$inventorId, "invention"=>$invention,
                "description"=>$description);
            $connection->commit();
            $connection = null;
        } catch (PDOException $exception) {
            return null;
        }
        return $result;
    }

    public function updateInventor($id, $name, $surname, $birth, $birthplace, $death, $deathplace, $description) {
        $result = null;
        try {
            $connection = new PDO(
                "mysql:host=$this->serverName;dbname=$this->databaseName",
                $this->userName,
                $this->userPassword
            );
            $connection->beginTransaction();
            $statement = $connection->prepare(
                "UPDATE inventors SET name = ?, surname = ?, birth = ?, birthplace = ?, death = ?, deathplace = ?,
                        description = ? WHERE id = ?");
            $statement->execute([$name, $surname, $birth, $birthplace, $death, $deathplace, $description, $id]);
            $connection->commit();

            $statement = $connection->prepare(
                "SELECT * FROM inventors WHERE id = ?");
            $statement->execute([$id]);
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $result = $statement->fetch();

            $connection = null;
        } catch (PDOException $exception) {
            return null;
        }
        return $result;
    }

    public function deleteInventor($id) {
        try {
            $connection = new PDO(
                "mysql:host=$this->serverName;dbname=$this->databaseName",
                $this->userName,
                $this->userPassword
            );
            $connection->beginTransaction();
            $statement = $connection->prepare("DELETE FROM inventors WHERE id = ?");
            $statement->execute([$id]);
            $connection->commit();
            $connection = null;
        } catch (PDOException $exception) {
            return null;
        }
    }
}