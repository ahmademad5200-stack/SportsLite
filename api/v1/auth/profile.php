<?php
require_once "../Helpers/headers.php";
send_json_api_headers('GET'); // العرض دائماً GET

require_once "../config/conn.php";
require_once "../Helpers/response.php";

// بناخد الـ id من الرابط (مثلاً profile.php?id=4)
$user_id = $_GET['id'] ?? null;

if (!$user_id) {
    response(400, "User ID is required");
}

try {
    $stmt = $pdo->prepare("SELECT id, name, email, phone, gender, account_status FROM trainees WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        response(200, "Profile data retrieved", $user);
    } else {
        response(404, "User not found");
    }
} catch (PDOException $e) {
    response(500, "Database error: " . $e->getMessage());
}