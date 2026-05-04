<?php
require_once "../Helpers/headers.php";
send_json_api_headers('GET');

require_once "../config/conn.php";
require_once "../Helpers/response.php";

$name = isset($_GET['name']) ? $_GET['name'] : '';

try {
    $query = "SELECT * FROM `subscription_plans` WHERE `name` LIKE :name";
    $stmt = $pdo->prepare($query);
    $search_term = "%$name%";
    $stmt->bindParam(':name', $search_term, PDO::PARAM_STR);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    response(200, "Search results.", $results);
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}