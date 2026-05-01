<?php
require_once "../Helpers/headers.php";
send_json_api_headers('GET');
require_once "../Config/conn.php";
require_once "../Helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    response(405, "Only GET Method is allowed");
}

try {
    $sql = $pdo->prepare("SELECT * FROM `branches` ORDER BY id DESC"); 
    $sql->execute(); 
    $data = $sql->fetchAll(); 

    if (count($data) > 0) {
        response(200, "Branches retrieved successfully", ["data" => $data]);
    } else {
        response(404, "No Branches Found", ["data" => []]);
    }
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}