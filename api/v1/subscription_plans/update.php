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
    if (!isset($input->id) || !isset($input->name)) {
        response(400, "Bad request: 'id' and 'name' are required");
    }

    $id = $input->id;
    $name = $input->name;
    $discount = isset($input->discount_percentage) ? $input->discount_percentage : 0;
    $duration = isset($input->duration_days) ? $input->duration_days : 30;

    if (!is_numeric($id) || !is_string($name)) {
        response(400, "Bad request: check your data types.");
    }

    // Check if exists
    $check = $pdo->prepare("SELECT `id` FROM `subscription_plans` WHERE `id` = :id");
    $check->bindParam(":id", $id, PDO::PARAM_INT);
    $check->execute();

    if ($check->rowCount() === 0) {
        response(404, "Bad request: Plan not found!");
    }

    $query = "UPDATE `subscription_plans` SET `name` = :name, `discount_percentage` = :discount, `duration_days` = :duration WHERE `id` = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->bindParam(":name", $name, PDO::PARAM_STR);
    $stmt->bindParam(":discount", $discount);
    $stmt->bindParam(":duration", $duration, PDO::PARAM_INT);

    if ($stmt->execute()) {
        response(200, "Plan updated successfully.");
    } else {
        response(503, "Unable to update the plan");
    }
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}