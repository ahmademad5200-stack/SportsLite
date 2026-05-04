<?php
require_once "../Helpers/headers.php";
send_json_api_headers('PUT'); // متوافق مع .htaccess

require_once "../config/conn.php";
require_once "../Helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "PUT") {
    response(405, "Only PUT Method is allowed");
}

$input = json_decode(file_get_contents("php://input"));

try {
    if (!isset($input->id)) {
        response(400, "Bad request: 'id' is required");
    }

    $id = $input->id;
    $name = $input->name;
    $experience = $input->years_of_experience;
    $spec = $input->specialization;
    $rating = $input->rating;

    // التأكد من وجود السجل
    $check = $pdo->prepare("SELECT id FROM trainers WHERE id = :id");
    $check->execute([':id' => $id]);
    if ($check->rowCount() === 0) response(404, "Trainer not found!");

    $query = "UPDATE trainers SET name = :name, years_of_experience = :exp, specialization = :spec, rating = :rating WHERE id = :id";
    $stmt = $pdo->prepare($query);
    
    if ($stmt->execute([':name'=>$name, ':exp'=>$experience, ':spec'=>$spec, ':rating'=>$rating, ':id'=>$id])) {
        response(200, "Trainer updated successfully.");
    } else {
        response(503, "Unable to update trainer");
    }
} catch (Exception $e) {
    response(500, "Server error: " . $e->getMessage());
}