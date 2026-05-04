<?php
require_once "../Helpers/headers.php";
send_json_api_headers('POST');
require_once "../Config/conn.php";
require_once "../Helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") 
    { response(405, "Only POST Method is allowed"); }

$input = json_decode(file_get_contents("php://input"));

try {
    if(!isset($input->title)){ response(400, "Title is required"); }

    $query = "INSERT INTO `health_news` (`title`, `description`, `image_path`, `category_id`) VALUES (:title, :descr, :path, :cid)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':title' => $input->title,
        ':descr' => $input->description ?? null,
        ':path'  => $input->image_path ?? null,
        ':cid'   => $input->category_id ?? null
    ]);

    response(200, "News created successfully.");
} catch (Exception $e) { response(500, "server error: " . $e->getMessage()); }