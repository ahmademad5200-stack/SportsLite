<?php
require_once "../Helpers/headers.php";
send_json_api_headers('DELETE');
require_once "../Config/conn.php";
require_once "../Helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "DELETE") {
    response(405, "Only DELETE Method is allowed");
}

$input = json_decode(file_get_contents("php://input"));

try {
    if(!isset($input->id)){
        response(400, "Bad request: 'id' is required");
    }
    $id = $input->id;

    $check = $pdo->prepare("SELECT `id` FROM `trainee_programs` WHERE `id` = :id");
    $check->execute([':id' => $id]);

    if($check->rowCount() === 0){
        response(404, "trainee_programs not found!");
        exit;
    }

    $delete_query = "DELETE FROM `trainee_programs` WHERE `id` = :id";
    $delete_stmnt = $pdo->prepare($delete_query);
    $delete_stmnt->execute([':id' => $id]);
    
    response(200, "trainee_programs deleted successfully.");
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}