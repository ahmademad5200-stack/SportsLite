<?php
require_once "../Helpers/headers.php";
send_json_api_headers('PUT'); // حسب .htaccess

require_once "../config/conn.php";
require_once "../Helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "PUT") {
    response(405, "Only PUT Method is allowed");
}

$input = json_decode(file_get_contents("php://input"));

try {
    if (!isset($input->id) || !isset($input->name)) {
        response(400, "Bad request: 'id' and 'name' are required");
    }

    $id = $input->id;
    $name = $input->name;

    $check = $pdo->prepare("SELECT `id` FROM `news_categories` WHERE `id` = :id");
    $check->bindParam(":id", $id, PDO::PARAM_INT);
    $check->execute();

    if ($check->rowCount() === 0) {
        response(404, "Bad request: Category not found!");
    }

    $query = "UPDATE `news_categories` SET `name` = :name WHERE `id` = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->bindParam(":name", $name, PDO::PARAM_STR);

    if ($stmt->execute()) {
        response(200, "Category updated successfully.");
    } else {
        response(503, "Unable to update the category");
    }
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}