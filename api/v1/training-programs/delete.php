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
        response(400, "Bad request: 'id' is required");
    }

    $id = $input->id;

    $check = $pdo->prepare("SELECT id FROM training_programs WHERE id = :id");
    $check->execute([':id' => $id]);

    if ($check->rowCount() === 0) {
        response(404, "Program not found!");
    }

    $stmt = $pdo->prepare("DELETE FROM training_programs WHERE id = :id");
    if ($stmt->execute([':id' => $id])) {
        response(200, "Program deleted successfully.");
    } else {
        response(503, "Unable to delete program");
    }
} catch (Exception $e) {
    response(500, "Server error: " . $e->getMessage());
}