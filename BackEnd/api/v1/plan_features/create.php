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
    if(!isset($input->description) || !isset($input->plan_id)){
        response(400, "Bad request: 'description' and 'plan_id' are required");
    }

    $description = $input->description;
    $plan_id = $input->plan_id;

    $query = "INSERT INTO `plan_features` (`description`, `plan_id`) VALUES (:description, :plan_id)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':description', $description, PDO::PARAM_STR);
    $stmt->bindParam(':plan_id', $plan_id, PDO::PARAM_INT);

    if($stmt->execute()){
        response(200, "Plan feature created successfully.", ["id" => $pdo->lastInsertId()]);
    } else {
        response(503, "Unable to create the feature");
    }
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}