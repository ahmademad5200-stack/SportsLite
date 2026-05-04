<?php
require_once "../Helpers/headers.php";
send_json_api_headers('GET');

require_once "../config/conn.php";
require_once "../Helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    response(405, "Only GET Method is allowed");
}

$term = isset($_GET['term']) ? $_GET['term'] : '';

try {
    // بحث بالإسم أو الإيميل
    $query = "SELECT * FROM `messages` WHERE `name` LIKE :term OR `email` LIKE :term ORDER BY `id` DESC";
    $stmt = $pdo->prepare($query);
    $search_term = "%$term%";
    $stmt->bindParam(':term', $search_term, PDO::PARAM_STR);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    response(200, "Search results.", $results);
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}