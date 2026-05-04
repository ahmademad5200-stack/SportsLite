<?php
require_once "../Helpers/headers.php";
send_json_api_headers('POST');

require_once "../config/conn.php";
require_once "../Helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    response(405, "Only POST Method is allowed");
}

$input = json_decode(file_get_contents("php://input"));

try {
    // الحقول المطلوبة بناءً على طبيعة الجدول
    if(!isset($input->name) || !isset($input->years_of_experience) || !isset($input->specialization)){
        response(400, "Bad request: 'name', 'years_of_experience', and 'specialization' are required");
    }

    $name = $input->name;
    $experience = $input->years_of_experience;
    $specialization = $input->specialization;
    $rating = isset($input->rating) ? $input->rating : 0.00;
    $image_path = isset($input->image_path) ? $input->image_path : null;
    $description = isset($input->description) ? $input->description : null;
    $gender = isset($input->gender) ? $input->gender : 'Male';

    $query = "INSERT INTO trainers (name, years_of_experience, rating, image_path, description, specialization, gender) 
              VALUES (:name, :experience, :rating, :img, :descr, :spec, :gender)";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':experience', $experience, PDO::PARAM_INT);
    $stmt->bindParam(':rating', $rating);
    $stmt->bindParam(':img', $image_path);
    $stmt->bindParam(':descr', $description);
    $stmt->bindParam(':spec', $specialization);
    $stmt->bindParam(':gender', $gender);

    if($stmt->execute()){
        response(200, "Trainer added successfully.", ["id" => $pdo->lastInsertId()]);
    } else {
        response(503, "Unable to add trainer");
    }
} catch (Exception $e) {
    response(500, "Server error: " . $e->getMessage());
}