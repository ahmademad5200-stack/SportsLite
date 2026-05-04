<?php
require_once "../Helpers/headers.php";
send_json_api_headers('PUT');

require_once "../Config/conn.php";
require_once "../Helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "PUT") {
    response(405, "Only PUT Method is allowed for updating news");
}

$input = json_decode(file_get_contents("php://input"));

try {
    if (!isset($input->id)) {
        response(400, "Bad request: 'id' is required to update the news article");
    }

    $id = $input->id;

    $check = $pdo->prepare("SELECT `id` FROM `health_news` WHERE `id` = :id");
    $check->execute([':id' => $id]);

    if ($check->rowCount() === 0) {
        response(404, "News article with ID: $id not found!");
        exit;
    }

    $title       = isset($input->title) ? $input->title : null;
    $description = isset($input->description) ? $input->description : null;
    $image_path  = isset($input->image_path) ? $input->image_path : null;
    $category_id = isset($input->category_id) ? $input->category_id : null;

    $update_query = "UPDATE `health_news` 
                     SET `title` = :title, 
                         `description` = :descr, 
                         `image_path` = :path, 
                         `category_id` = :cid 
                     WHERE `id` = :id";

    $update_stmnt = $pdo->prepare($update_query);
    
    $result = $update_stmnt->execute([
        ':title' => $title,
        ':descr' => $description,
        ':path'  => $image_path,
        ':cid'   => $category_id,
        ':id'    => $id
    ]);

    if ($result) {
        response(200, "News article updated successfully.");
    } else {
        response(503, "Unable to update the news article.");
    }

} catch (Exception $e) {
    // معالجة أي خطأ غير متوقع في السيرفر
    response(500, "Server error: " . $e->getMessage());
}