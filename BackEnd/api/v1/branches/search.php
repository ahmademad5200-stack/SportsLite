<?php
require_once "../Helpers/headers.php";
send_json_api_headers('GET');
require_once "../Config/conn.php";
require_once "../Helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    response(405, "Only GET Method is allowed");
}

try {
    $search_term = isset($_GET['name']) ? $_GET['name'] : '';

    if(empty($search_term)){
        response(400, "Bad request: 'name' query parameter is required for search");
        exit;
    }

    $query = "SELECT * FROM `branches` WHERE `name` LIKE :search";
    $stmt = $pdo->prepare($query);
    $search_wildcard = "%" . $search_term . "%";
    $stmt->bindParam(':search', $search_wildcard, PDO::PARAM_STR);
    $stmt->execute();
    
    $data = $stmt->fetchAll();
    
    if (count($data) > 0) {
        response(200, "Search results found", ["data" => $data]);
    } else {
        response(404, "No results found for this search", ["data" => []]);
    }
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}