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
    if (!isset($input->id) || !isset($input->amount) || !isset($input->payment_status)) {
        response(400, "Bad request: 'id', 'amount', and 'payment_status' are required");
    }

    $id = $input->id;
    $amount = $input->amount;
    $status = $input->payment_status;

    // التأكد من وجود السجل قبل التعديل
    $check = $pdo->prepare("SELECT `id` FROM `payments` WHERE `id` = :id");
    $check->bindParam(":id", $id, PDO::PARAM_INT);
    $check->execute();

    if ($check->rowCount() === 0) {
        response(404, "Bad request: payment not found!");
    }

    $update_query = "UPDATE `payments` SET `amount` = :amount, `payment_status` = :status WHERE `id` = :id";
    $update_stmnt = $pdo->prepare($update_query);
    $update_stmnt->bindParam(":id", $id, PDO::PARAM_INT);
    $update_stmnt->bindParam(":amount", $amount);
    $update_stmnt->bindParam(":status", $status, PDO::PARAM_STR);

    if ($update_stmnt->execute()) {
        response(200, "Payment updated successfully.");
    } else {
        response(503, "Unable to update the payment");
    }
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}