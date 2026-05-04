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

    // التأكد من وجود المدرب قبل الحذف
    $check = $pdo->prepare("SELECT id FROM trainers WHERE id = :id");
    $check->execute([':id' => $id]);

    if ($check->rowCount() === 0) {
        response(404, "Trainer not found!");
    }

    $stmt = $pdo->prepare("DELETE FROM trainers WHERE id = :id");
    if ($stmt->execute([':id' => $id])) {
        response(200, "Trainer deleted successfully.");
    } else {
        response(503, "Unable to delete trainer");
    }
} catch (Exception $e) {
    response(500, "Server error: " . $e->getMessage());
}