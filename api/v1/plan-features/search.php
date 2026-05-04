<?php
require_once "../Helpers/headers.php";
send_json_api_headers('GET');

require_once "../config/conn.php";
require_once "../Helpers/response.php";

$term = isset($_GET['description']) ? $_GET['description'] : '';

try {
    $query = "SELECT * FROM `plan_features` WHERE `description` LIKE :term";
    $stmt = $pdo->prepare($query);
    $search_term = "%$term%";
    $stmt->bindParam(':term', $search_term, PDO::PARAM_STR);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    response(200, "Search results.", $results);
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}