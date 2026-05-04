<?php
require_once "../Helpers/headers.php";
send_json_api_headers('GET');
require_once "../config/conn.php";
require_once "../Helpers/response.php";

try {
    $sql = "SELECT id, name, years_of_experience, rating, specialization, gender FROM trainers";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $trainers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    response(200, "Trainers retrieved successfully", $trainers);
} catch (PDOException $e) {
    response(500, "Server error: " . $e->getMessage());
}