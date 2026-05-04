<?php
require_once "../Helpers/headers.php";
send_json_api_headers('PUT');

require_once "../config/conn.php";
require_once "../Helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "PUT") {
    response(405, "Only PUT Method is allowed");
}

$input = json_decode(file_get_contents("php://input"));

try {
    if (!isset($input->id) || !isset($input->text_content) || !isset($input->rating)) {
        response(400, "Bad request: 'id', 'text_content', and 'rating' are required");
    }

    $id = $input->id;
    $text_content = $input->text_content;
    $rating = $input->rating;

    $check = $pdo->prepare("SELECT `id` FROM `reviews` WHERE `id` = :id");
    $check->bindParam(":id", $id, PDO::PARAM_INT);
    $check->execute();

    if ($check->rowCount() === 0) {
        response(404, "Bad request: Review not found!");
    }

    $query = "UPDATE `reviews` SET `text_content` = :text_content, `rating` = :rating WHERE `id` = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->bindParam(":text_content", $text_content, PDO::PARAM_STR);
    $stmt->bindParam(":rating", $rating, PDO::PARAM_INT);

    if ($stmt->execute()) {
        response(200, "Review updated successfully.");
    } else {
        response(503, "Unable to update the review");
    }
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}