<?php
header('Content-Type: application/json; charset=utf-8');
require_once "../PDOManager.php";

$config = require_once "../config.php";
$serverName = $config["serverName"];
$userName = $config["userName"];
$userPassword = $config["userPassword"];
$databaseName = $config["databaseName"];
$controllerPDO = new PDOManager($serverName, $userName, $userPassword, $databaseName);

switch ($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        if (isset($_GET["id"])) {
            header("HTTP/1.1 200 OK");
            $result = $controllerPDO->findInventorWithInventionsById($_GET["id"]);
            if ($result == null)
                header("HTTP/1.1 404 Not found");
            echo json_encode($result);
        } elseif (isset($_GET["surname"])) {
            header("HTTP/1.1 200 OK");
            $result = $controllerPDO->findInventorBySurname($_GET["surname"]);
            if ($result == null)
                header("HTTP/1.1 404 Not found");
            echo json_encode($result);
        } else {
            header("HTTP/1.1 200 OK");
            $result = $controllerPDO->findAllInventors();
            if ($result == null)
                header("HTTP/1.1 404 Not found");
            echo json_encode($result);
        }
        break;

    case "POST":
        header("HTTP/1.1 201 OK");
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $controllerPDO->insertInventor($data["name"], $data["surname"], $data["birth"], $data["birthplace"],
            $data["death"], $data["deathplace"], $data["description"]);
        if ($result == null) {
            header("HTTP/1.1 404 Not found");
        } else {
            $tmpInventions = [];
            foreach ($data["inventions"] as $invention) {
                $tmpResult = $controllerPDO->insertInvention($result["id"], $invention["invention"], $invention["description"]);
                if ($tmpResult == null)
                    header("HTTP/1.1 404 Not found");
                array_push($tmpInventions, $tmpResult);
            }
            $result["inventions"] = $tmpInventions;
        }
        echo json_encode($result);
        break;

    case "PUT":
        header("HTTP/1.1 201 OK");
        if (isset($_GET["id"])) {
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $controllerPDO->updateInventor($_GET["id"], $data["name"], $data["surname"], $data["birth"],
                $data["birthplace"], $data["death"], $data["deathplace"], $data["description"]);
            if ($result == null)
                header("HTTP/1.1 404 Not found");
            echo json_encode($result);
        }
        break;

    case "DELETE":
        if (isset($_GET["id"])) {
            header("HTTP/1.1 204 OK");
            $result = $controllerPDO->deleteInventor($_GET["id"]);
        }
        break;
}