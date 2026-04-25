<?php
require_once "../Helpers/headers.php";
send_json_api_headers('GET');

require_once "../Config/conn.php";
require_once "../Helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    response(405, "Only GET Method is allowed");
}

try {
    $query = "SELECT * FROM `payments`"; 
    $sql = $pdo->prepare($query);
    $sql->execute();
    $data = $sql->fetchAll();

    if (count($data) > 0) {
        response(200, "Payments retrieved successfully", [
            "count" => count($data),
            "data" => $data,
        ]);
    } else {
        response(404, "No Payments Found", ["data" => []]);
    }
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}