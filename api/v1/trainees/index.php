<?php
require_once "../Helpers/headers.php";
send_json_api_headers('GET');

require_once "../config/conn.php";
require_once "../Helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    response(405, "Only GET Method is allowed");
}

try {
    // إخفاء الـ password_hash من النتائج لزيادة الأمان
    $query = "SELECT `id`, `name`, `email`, `phone`, `first_visit_date`, `account_status`, `birth_date`, `gender` FROM `trainees`";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    response(200, "Trainees retrieved successfully.", $data);
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}