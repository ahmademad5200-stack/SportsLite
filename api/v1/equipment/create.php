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
    if(!isset($input->name) || !isset($input->branch_id)){
        response(400, "Bad request: 'name' and 'branch_id' are required");
    }

    $name = $input->name;
    $branch_id = $input->branch_id;
    $image_path = isset($input->image_path) ? $input->image_path : null;
    $target_muscles = isset($input->target_muscles) ? $input->target_muscles : null;
    $usage_instructions = isset($input->usage_instructions) ? $input->usage_instructions : null;
    $usage_image_path = isset($input->usage_image_path) ? $input->usage_image_path : null;

    $query = "INSERT INTO equipment (name, branch_id, image_path, target_muscles, usage_instructions, usage_image_path) 
              VALUES (:name, :branch_id, :img, :muscles, :instr, :u_img)";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    $stmt->bindParam(':img', $image_path);
    $stmt->bindParam(':muscles', $target_muscles);
    $stmt->bindParam(':instr', $usage_instructions);
    $stmt->bindParam(':u_img', $usage_image_path);

    if($stmt->execute()){
        response(200, "Equipment added successfully.", ["id" => $pdo->lastInsertId()]);
    } else {
        response(503, "Unable to add equipment");
    }
} catch (Exception $e) {
    response(500, "Server error: " . $e->getMessage());
}