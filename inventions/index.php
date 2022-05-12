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
        if (isset($_GET["century"])) {
            header("HTTP/1.1 200 OK");
            $result = $controllerPDO->findInventionsByCentury($_GET["century"]);
            if ($result == null)
                header("HTTP/1.1 404 Not found");
            echo json_encode($result);
        }
        break;

    case "POST":
        if (isset($_GET["inventor_id"])) {
            header("HTTP/1.1 201 OK");
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $controllerPDO->insertInvention($_GET["inventor_id"], $data["invention"], $data["description"]);
            if ($result == null)
                header("HTTP/1.1 404 Not found");
            echo json_encode($result);
        }
        break;
}