<?php
require_once "../Helpers/headers.php";
send_json_api_headers('POST');

require_once "../config/conn.php";
require_once "../Helpers/response.php";

$input = json_decode(file_get_contents("php://input"));

if (empty($input->email) || empty($input->new_password)) {
    response(400, "Email and New Password are required");
}

try {
    // تشفير الباسورد الجديد
    $new_hash = password_hash($input->new_password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("UPDATE trainees SET password_hash = ? WHERE email = ?");
    $stmt->execute([$new_hash, $input->email]);

    if ($stmt->rowCount() > 0) {
        response(200, "Password updated successfully");
    } else {
        response(404, "User not found");
    }
} catch (PDOException $e) {
    response(500, "Database error: " . $e->getMessage());
}