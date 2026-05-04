<?php
require_once "../Helpers/headers.php";
send_json_api_headers('PUT'); // تعديل بناء على htaccess
require_once "../Config/conn.php";
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
    $name = isset($input->name) ? $input->name : null;
    $address = isset($input->address) ? $input->address : null;
    $phone = isset($input->phone) ? $input->phone : null;
    $work_hours = isset($input->work_hours) ? $input->work_hours : null;

    $check = $pdo->prepare("SELECT `id` FROM `branches` WHERE `id` = :id");
    $check->execute([':id' => $id]);

    if ($check->rowCount() === 0) {
        response(404, "Branch ID: $id not found!");
        exit;
    }

    $update_query = "UPDATE `branches` SET `name` = :name, `address` = :address, `phone` = :phone, `work_hours` = :work_hours WHERE `id` = :id";
    $update_stmnt = $pdo->prepare($update_query);
    $update_stmnt->execute([
        ':name' => $name,
        ':address' => $address,
        ':phone' => $phone,
        ':work_hours' => $work_hours,
        ':id' => $id
    ]);

    response(200, "Branch updated successfully.");
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}