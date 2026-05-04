<?php
require_once "../Helpers/headers.php";
send_json_api_headers('POST');
require_once "../Config/conn.php";
require_once "../Helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") { response(405, "Only POST Method is allowed"); }

$input = json_decode(file_get_contents("php://input"));

try {
    if(!isset($input->trainee_id) || !isset($input->program_id) || !isset($input->enrollment_date)){
        response(400, "Bad request: trainee_id, program_id, and enrollment_date are required");
    }

    $query = "INSERT INTO `trainee_programs` (`trainee_id`, `program_id`, `enrollment_date`) VALUES (:tid, :pid, :edate)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':tid' => $input->trainee_id,
        ':pid' => $input->program_id,
        ':edate' => $input->enrollment_date
    ]);

    response(200, "Program assigned successfully.", ["id" => $pdo->lastInsertId()]);
} catch (Exception $e) { response(500, "server error: " . $e->getMessage()); }