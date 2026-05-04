<?php
require_once "../Helpers/headers.php";
send_json_api_headers('POST');

require_once "../Config/conn.php";
require_once "../Helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    response(405, "Only POST Method is allowed");
}

$input = json_decode(file_get_contents("php://input"));

try {
    // التحقق من الحقول الإجبارية
    if(!isset($input->name) || !isset($input->email) || !isset($input->message_text)){
        response(400, "Bad request: 'name', 'email', and 'message_text' are required");
    }

    $name = $input->name;
    $email = $input->email;
    $message_text = $input->message_text;
    
    // الحقول الاختيارية
    $trainee_id = isset($input->trainee_id) ? $input->trainee_id : null;
    $phone = isset($input->phone) ? $input->phone : null;

    $query = "INSERT INTO `messages` (`trainee_id`, `name`, `email`, `phone`, `message_text`, `message_date`, `is_read`) 
              VALUES (:trainee_id, :name, :email, :phone, :message_text, NOW(), 0)";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':trainee_id', $trainee_id);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':message_text', $message_text, PDO::PARAM_STR);

    if($stmt->execute()){
        response(200, "Message created successfully.", ["id" => $pdo->lastInsertId()]);
    } else {
        response(503, "Unable to create the message");
    }
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}