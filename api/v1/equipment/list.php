<?php
require_once "../Helpers/headers.php";
send_json_api_headers('GET');

require_once "../config/conn.php";
require_once "../Helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    response(405, "Only GET Method is allowed");
}

try {
    $stmt = $pdo->query("SELECT * FROM equipment");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    response(200, "Equipment list retrieved.", $data);
} catch (Exception $e) {
    response(500, "Server error: " . $e->getMessage());
}