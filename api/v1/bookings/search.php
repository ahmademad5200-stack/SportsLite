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

    $query = "SELECT * FROM `bookings` WHERE `trainee_id` = :tid";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':tid', $trainee_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $data = $stmt->fetchAll();
    response(200, "Search results for trainee", ["data" => $data]);
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}