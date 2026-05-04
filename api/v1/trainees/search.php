<?php
require_once "../Helpers/headers.php";
send_json_api_headers('GET');

require_once "../config/conn.php";
require_once "../Helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    response(405, "Only GET Method is allowed");
}

$name = isset($_GET['name']) ? $_GET['name'] : '';

try {
    $query = "SELECT `id`, `name`, `email`, `phone`, `first_visit_date`, `account_status`, `birth_date`, `gender` FROM trainees WHERE name LIKE :name";
    $stmt = $pdo->prepare($query);
    
    $search_term = "%$name%";
    $stmt->bindParam(':name', $search_term, PDO::PARAM_STR);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($results) > 0) {
        response(200, "Search results retrieved successfully.", $results);
    } else {
        response(404, "No trainees found matching the search criteria.");
    }
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}