<?php
require_once "../Helpers/headers.php";
send_json_api_headers('POST');

require_once "../config/conn.php";
require_once "../Helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    response(405, "Only POST Method is allowed");
}

$input = json_decode(file_get_contents("php://input"));

try {
    if(!isset($input->name) || empty(trim($input->name))){
        response(400, "Bad request: 'name' is required");
    }

    $name = $input->name;

    $query = "INSERT INTO `news_categories` (`name`) VALUES (:name)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);

    if($stmt->execute()){
        response(200, "News category created successfully.", ["id" => $pdo->lastInsertId()]);
    } else {
        response(503, "Unable to create the category");
    }
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}