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
    if (!isset($input->id) || !isset($input->name)) {
        response(400, "Bad request: 'id' and 'name' are required");
    }

    $id = $input->id;
    $name = $input->name;
    $branch_id = $input->branch_id;
    $target = $input->target_muscles;

    $check = $pdo->prepare("SELECT id FROM equipment WHERE id = :id");
    $check->execute([':id' => $id]);
    if ($check->rowCount() === 0) response(404, "Equipment not found!");

    $query = "UPDATE equipment SET name = :name, branch_id = :bid, target_muscles = :target WHERE id = :id";
    $stmt = $pdo->prepare($query);
    
    if ($stmt->execute([':name'=>$name, ':bid'=>$branch_id, ':target'=>$target, ':id'=>$id])) {
        response(200, "Equipment updated successfully.");
    } else {
        response(503, "Unable to update equipment");
    }
} catch (Exception $e) {
    response(500, "Server error: " . $e->getMessage());
}