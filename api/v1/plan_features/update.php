<?php
require_once "../Helpers/headers.php";
send_json_api_headers('PUT');

require_once "../config/conn.php";
require_once "../Helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "PUT") {
    response(405, "Only PUT Method is allowed");
}

$input = json_decode(file_get_contents("php://input"));

try {
    if (!isset($input->id) || !isset($input->description) || !isset($input->plan_id)) {
        response(400, "Bad request: 'id', 'description' and 'plan_id' are required");
    }

    $id = $input->id;
    $description = $input->description;
    $plan_id = $input->plan_id;

    $check = $pdo->prepare("SELECT `id` FROM `plan_features` WHERE `id` = :id");
    $check->bindParam(":id", $id, PDO::PARAM_INT);
    $check->execute();

    if ($check->rowCount() === 0) {
        response(404, "Bad request: Feature not found!");
    }

    $query = "UPDATE `plan_features` SET `description` = :description, `plan_id` = :plan_id WHERE `id` = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->bindParam(":description", $description, PDO::PARAM_STR);
    $stmt->bindParam(":plan_id", $plan_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        response(200, "Feature updated successfully.");
    } else {
        response(503, "Unable to update the feature");
    }
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}