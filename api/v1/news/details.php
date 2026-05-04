<?php
require_once "../Helpers/headers.php";
send_json_api_headers('GET');
require_once "../Config/conn.php";
require_once "../Helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    response(405, "Only GET Method is allowed");
}

try {
    $search_term = isset($_GET['q']) ? $_GET['q'] : '';

    if(empty($search_term)){
        response(400, "Bad request: 'q' parameter is required for searching news");
        exit;
    }

    $query = "SELECT * FROM `health_news` WHERE `title` LIKE :search OR `description` LIKE :search";
    $stmt = $pdo->prepare($query);
    $search_wildcard = "%" . $search_term . "%";
    $stmt->bindParam(':search', $search_wildcard, PDO::PARAM_STR);
    $stmt->execute();
    
    $data = $stmt->fetchAll();
    
    if (count($data) > 0) {
        response(200, "News articles found", ["data" => $data]);
    } else {
        response(404, "No news matches your search", ["data" => []]);
    }
} catch (Exception $e) {
    response(500, "Server error: " . $e->getMessage());
}