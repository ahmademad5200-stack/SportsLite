<?php
require_once "../Helpers/headers.php";
send_json_api_headers('DELETE');
require_once "../config/conn.php";
require_once "../Helpers/response.php";

$input = json_decode(file_get_contents("php://input"));

try {
    if (empty($input->id)) {
        response(400, "Trainer ID is required for deletion");
    }

    $sql = "DELETE FROM trainers WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $input->id]);

    response(200, "Trainer deleted successfully");
} catch (PDOException $e) {
    // ملاحظة: قد يفشل الحذف إذا كان المدرب مرتبطاً بحجوزات (Foreign Key)
    response(500, "Server error: " . $e->getMessage());
}