<?php
require_once "../Helpers/headers.php";
send_json_api_headers('GET');
require_once "../config/conn.php";
require_once "../Helpers/response.php";

$name = $_GET['name'] ?? '';

try {
    $sql = "SELECT * FROM trainers WHERE name LIKE :name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':name' => "%$name%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    response(200, "Search results", $results);
} catch (PDOException $e) {
    response(500, "Server error: " . $e->getMessage());
}