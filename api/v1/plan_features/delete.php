<?php
require_once "../Helpers/headers.php";
send_json_api_headers('DELETE');

require_once "../config/conn.php";
require_once "../Helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "DELETE") {
    response(405, "Only DELETE Method is allowed");
}

$input = json_decode(file_get_contents("php://input"));

try {
    if (!isset($input->id)) {
        response(400, "Bad request: 'id' is required for deletion");
    }

    $id = $input->id;

    $check = $pdo->prepare("SELECT `id` FROM `plan_features` WHERE `id` = :id");
    $check->bindParam(":id", $id, PDO::PARAM_INT);
    $check->execute();

    if ($check->rowCount() === 0) {
        response(404, "Bad request: Feature not found!");
    }

    $query = "DELETE FROM `plan_features` WHERE `id` = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        response(200, "Feature deleted successfully.");
    } else {
        response(503, "Unable to delete the feature");
    }
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}