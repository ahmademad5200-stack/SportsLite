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
    if(!isset($input->text_content) || !isset($input->rating) || !isset($input->trainee_id)){
        response(400, "Bad request: 'text_content', 'rating', and 'trainee_id' are required");
    }

    $text_content = $input->text_content;
    $rating = $input->rating;
    $trainee_id = $input->trainee_id;

    $query = "INSERT INTO `reviews` (`text_content`, `rating`, `trainee_id`, `publish_date`) 
              VALUES (:text_content, :rating, :trainee_id, NOW())";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':text_content', $text_content, PDO::PARAM_STR);
    $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
    $stmt->bindParam(':trainee_id', $trainee_id, PDO::PARAM_INT);

    if($stmt->execute()){
        response(200, "Review created successfully.", ["id" => $pdo->lastInsertId()]);
    } else {
        response(503, "Unable to create the review");
    }
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}