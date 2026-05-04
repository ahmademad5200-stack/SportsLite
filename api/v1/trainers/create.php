<?php
require_once "../Helpers/headers.php";
send_json_api_headers('POST');

require_once "../config/conn.php";
require_once "../Helpers/response.php";

$input = json_decode(file_get_contents("php://input"));

try {
    if (empty($input->full_name)) {
        response(400, "Full name is required");
    }

    // تم حذف branch_id لأن الجدول لا يحتويه حسب الصور
    $sql = "INSERT INTO trainers (name, years_of_experience, rating, specialization, gender) 
            VALUES (:name, :exp, :rating, :spec, :gender)";
    
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        ':name'   => $input->full_name,
        ':exp'    => $input->experience_years ?? 0,
        ':rating' => $input->rating ?? 0.0,
        ':spec'   => $input->specialization ?? '',
        ':gender' => $input->gender ?? 'Male'
    ]);

    response(201, "Trainer created successfully", ["id" => $pdo->lastInsertId()]);

} catch (PDOException $e) {
    response(500, "Server error: " . $e->getMessage());
}