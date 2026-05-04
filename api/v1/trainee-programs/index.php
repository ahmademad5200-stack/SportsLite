<?php
require_once "../Helpers/headers.php";
send_json_api_headers('GET');
require_once "../Config/conn.php";
require_once "../Helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    response(405, "Only GET Method is allowed");
}

try {
    $sql = $pdo->prepare("SELECT * FROM `trainee_programs` ORDER BY id DESC"); 
    $sql->execute(); 
    $data = $sql->fetchAll(); 

    if (count($data) > 0) {
        response(200, "trainee_programs retrieved successfully", ["data" => $data]);
    } else {
        response(404, "No trainee_programs Found", ["data" => []]);
    }
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}