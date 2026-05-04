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
    if (!isset($input->id)) {
        response(400, "Bad request: 'id' is required");
    }

    $id = $input->id;
    // الأعمدة الحقيقية بجدولك: plan_id, trainee_id, trainer_id
    $plan_id = isset($input->plan_id) ? $input->plan_id : null;
    $trainee_id = isset($input->trainee_id) ? $input->trainee_id : null;
    $trainer_id = isset($input->trainer_id) ? $input->trainer_id : null;

    $check = $pdo->prepare("SELECT `id` FROM `bookings` WHERE `id` = :id");
    $check->execute([':id' => $id]);

    if ($check->rowCount() === 0) {
        // هاد الـ 404 بيطلع لو الرقم مو موجود بقاعدة البيانات
        response(404, "Booking ID: $id not found in database!");
        exit;
    }

    $update_query = "UPDATE `bookings` SET `plan_id` = :pid, `trainee_id` = :tid, `trainer_id` = :trid WHERE `id` = :id";
    $update_stmnt = $pdo->prepare($update_query);
    $update_stmnt->execute([
        ':pid' => $plan_id,
        ':tid' => $trainee_id,
        ':trid' => $trainer_id,
        ':id'  => $id
    ]);

    response(200, "booking updated successfully.");
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}