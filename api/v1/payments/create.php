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
    if(!isset($input->trainee_id) || !isset($input->plan_id) || !isset($input->amount)){
        response(400, "Bad request: trainee_id, plan_id, and amount are required");
    }

    $trainee_id = $input->trainee_id;
    $plan_id = $input->plan_id;
    $amount = $input->amount;
    $method = isset($input->payment_method) ? $input->payment_method : 'cash';

    if(!is_numeric($trainee_id) || !is_numeric($plan_id) || !is_numeric($amount)){
        response(400, "Bad request: IDs and amount must be numeric.");
    }

    $create_query = "INSERT INTO `payments` (`trainee_id`, `plan_id`, `amount`, `payment_method`) VALUES (:t_id, :p_id, :amount, :method)";
    $create_stmnt = $pdo->prepare($create_query);
    $create_stmnt->bindParam(':t_id', $trainee_id, PDO::PARAM_INT);
    $create_stmnt->bindParam(':p_id', $plan_id, PDO::PARAM_INT);
    $create_stmnt->bindParam(':amount', $amount);
    $create_stmnt->bindParam(':method', $method, PDO::PARAM_STR);

    if($create_stmnt->execute()){
        response(200, "Payment recorded successfully.", ["id" => $pdo->lastInsertId()]);
    } else {
        response(503, "Unable to record the payment");
    }
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}