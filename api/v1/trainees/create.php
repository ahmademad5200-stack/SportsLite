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
    // التحقق من الحقول الأساسية المطلوبة (هنا افترضنا أن الاسم هو الإجباري)
    if(!isset($input->name)){
        response(400, "Bad request: 'name' is required");
    }

    $name = $input->name;
    $email = isset($input->email) ? $input->email : null;
    $phone = isset($input->phone) ? $input->phone : null;
    $password_hash = isset($input->password_hash) ? $input->password_hash : null;
    $first_visit_date = isset($input->first_visit_date) ? $input->first_visit_date : null;
    $account_status = isset($input->account_status) ? $input->account_status : 'Active';
    $birth_date = isset($input->birth_date) ? $input->birth_date : null;
    $gender = isset($input->gender) ? $input->gender : null;

    if(!is_string($name)){
        response(400, "Bad request: name must be string.");
    }

    $query = "INSERT INTO trainees (`name`, `email`, `phone`, `password_hash`, `first_visit_date`, `account_status`, `birth_date`, `gender`) 
              VALUES (:name, :email, :phone, :password_hash, :first_visit_date, :account_status, :birth_date, :gender)";
    $stmt = $pdo->prepare($query);
    
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
    $stmt->bindParam(':password_hash', $password_hash, PDO::PARAM_STR);
    $stmt->bindParam(':first_visit_date', $first_visit_date);
    $stmt->bindParam(':account_status', $account_status, PDO::PARAM_STR);
    $stmt->bindParam(':birth_date', $birth_date);
    $stmt->bindParam(':gender', $gender, PDO::PARAM_STR);

    if($stmt->execute()){
        response(200, "Trainee created successfully.", ["id" => $pdo->lastInsertId()]);
    } else {
        response(503, "Unable to create trainee");
    }
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}