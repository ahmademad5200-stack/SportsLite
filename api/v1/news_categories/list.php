<?php
require_once "../Helpers/headers.php";
send_json_api_headers('GET');

require_once "../config/conn.php";
require_once "../Helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    response(405, "Only GET Method is allowed");
}

try {
    $query = "SELECT * FROM `news_categories` ORDER BY `id` ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    response(200, "Categories retrieved successfully.", $data);
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}