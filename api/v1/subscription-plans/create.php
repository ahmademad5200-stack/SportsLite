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
    if(!isset($input->name) || !isset($input->duration_days)){
        response(400, "Bad request: 'name' and 'duration_days' are required");
    }

    $name = $input->name;
    $duration_days = $input->duration_days;
    $discount = isset($input->discount_percentage) ? $input->discount_percentage : 0;

    if(!is_string($name) || !is_numeric($duration_days)){
        response(400, "Bad request: name must be string and duration must be numeric.");
    }

    $query = "INSERT INTO `subscription_plans` (`name`, `discount_percentage`, `duration_days`) VALUES (:name, :discount, :duration)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':discount', $discount);
    $stmt->bindParam(':duration', $duration_days, PDO::PARAM_INT);

    if($stmt->execute()){
        response(200, "Subscription plan created successfully.", ["id" => $pdo->lastInsertId()]);
    } else {
        response(503, "Unable to create the plan");
    }
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}