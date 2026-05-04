<?php
require_once "../Helpers/headers.php";
send_json_api_headers('PUT');
require_once "../Config/conn.php";
require_once "../Helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "PUT") { response(405, "Only PUT Method is allowed"); }

$input = json_decode(file_get_contents("php://input"));

try {
    if (!isset($input->id)) { response(400, "id is required"); }

    $check = $pdo->prepare("SELECT `id` FROM `trainee_programs` WHERE `id` = :id");
    $check->execute([':id' => $input->id]);
    if ($check->rowCount() === 0) { response(404, "Record not found!"); exit; }

    $query = "UPDATE `trainee_programs` SET `trainee_id` = :tid, `program_id` = :pid, `enrollment_date` = :edate WHERE `id` = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':tid' => $input->trainee_id,
        ':pid' => $input->program_id,
        ':edate' => $input->enrollment_date,
        ':id' => $input->id
    ]);

    response(200, "Record updated successfully.");
} catch (Exception $e) { response(500, "server error: " . $e->getMessage()); }