<?php
require_once "../Helpers/headers.php";
send_json_api_headers('GET');
require_once "../Config/conn.php";
require_once "../Helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    response(405, "Only GET Method is allowed");
}

try {
    $trainee_id = isset($_GET['trainee_id']) ? $_GET['trainee_id'] : '';

    if(empty($trainee_id)){
        response(400, "Bad request: 'trainee_id' query parameter is required");
        exit;
    }

    $query = "SELECT * FROM `trainee_programs` WHERE `trainee_id` = :trainee_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':trainee_id', $trainee_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $data = $stmt->fetchAll();
    
    if (count($data) > 0) {
        response(200, "Enrollments found", ["data" => $data]);
    } else {
        response(404, "No programs found for this trainee", ["data" => []]);
    }
} catch (Exception $e) {
    response(500, "Server error: " . $e->getMessage());
}