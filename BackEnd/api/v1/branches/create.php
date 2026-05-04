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
    if(!isset($input->name)){
        response(400, "Bad request: name is required");
    }

    $name = $input->name;
    $address = isset($input->address) ? $input->address : null;
    $phone = isset($input->phone) ? $input->phone : null;
    $work_hours = isset($input->work_hours) ? $input->work_hours : null;

    $create_query = "INSERT INTO `branches` (`name`, `address`, `phone`, `work_hours`) 
                     VALUES (:name, :address, :phone, :work_hours)";
    
    $create_stmnt = $pdo->prepare($create_query);
    $create_stmnt->execute([
        ':name' => $name,
        ':address' => $address,
        ':phone' => $phone,
        ':work_hours' => $work_hours
    ]);

    response(200, "Branch created successfully.", ["id" => $pdo->lastInsertId()]);
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}