<?php
require_once "../Helpers/headers.php";
send_json_api_headers('PUT');
require_once "../config/conn.php";
require_once "../Helpers/response.php";

$input = json_decode(file_get_contents("php://input"));

try {
    if (empty($input->id)) {
        response(400, "Trainer ID is required for update");
    }

    $sql = "UPDATE trainers SET 
            name = :name, 
            years_of_experience = :exp, 
            specialization = :spec 
            WHERE id = :id";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $input->full_name,
        ':exp'  => $input->experience_years,
        ':spec' => $input->specialization,
        ':id'   => $input->id
    ]);

    response(200, "Trainer updated successfully");
} catch (PDOException $e) {
    response(500, "Server error: " . $e->getMessage());
}