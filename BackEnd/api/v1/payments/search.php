<?php
require_once "../Helpers/headers.php";
send_json_api_headers('GET');

require_once "../config/conn.php";
require_once "../Helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    response(405, "Only GET Method is allowed");
}

try {
    if (!isset($_GET['trainee_id'])) {
        response(400, "Bad request: 'trainee_id' is required to search payments");
    }

    $t_id = $_GET['trainee_id'];
    $query = "SELECT * FROM `payments` WHERE `trainee_id` = :t_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':t_id', $t_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $data = $stmt->fetchAll();
    response(200, "Payments for trainee retrieved", ["data" => $data]);
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}