<?php
require_once "../Helpers/headers.php";
send_json_api_headers('GET');

require_once "../config/conn.php";
require_once "../Helpers/response.php";

$title = isset($_GET['title']) ? $_GET['title'] : '';

try {
    $query = "SELECT * FROM training_programs WHERE title LIKE :title";
    $stmt = $pdo->prepare($query);
    $search_term = "%$title%";
    $stmt->execute([':title' => $search_term]);
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    response(200, "Search results.", $results);
} catch (Exception $e) {
    response(500, "Server error: " . $e->getMessage());
}