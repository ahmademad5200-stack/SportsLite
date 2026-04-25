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
    if(!isset($input->plan_id) || !isset($input->trainee_id) || !isset($input->start_date)){
        response(400, "Bad request: plan_id, trainee_id, and start_date are required");
    }

    $plan_id = $input->plan_id;
    $trainee_id = $input->trainee_id;
    $trainer_id = isset($input->trainer_id) ? $input->trainer_id : null;
    $start_date = $input->start_date;
    $end_date = isset($input->end_date) ? $input->end_date : null;
    $preferred_timing = isset($input->preferred_timing) ? $input->preferred_timing : null;

    $create_query = "INSERT INTO `bookings` (`plan_id`, `trainee_id`, `trainer_id`, `start_date`, `end_date`, `preferred_timing`) 
                    VALUES (:pid, :tid, :trid, :sdate, :edate, :timing)";
    
    $create_stmnt = $pdo->prepare($create_query);
    $create_stmnt->bindParam(':pid', $plan_id, PDO::PARAM_INT);
    $create_stmnt->bindParam(':tid', $trainee_id, PDO::PARAM_INT);
    $create_stmnt->bindParam(':trid', $trainer_id, PDO::PARAM_INT);
    $create_stmnt->bindParam(':sdate', $start_date, PDO::PARAM_STR);
    $create_stmnt->bindParam(':edate', $end_date, PDO::PARAM_STR);
    $create_stmnt->bindParam(':timing', $preferred_timing, PDO::PARAM_STR);

    if($create_stmnt->execute()){
        response(200, "booking created successfully.", ["id" => $pdo->lastInsertId()]);
    } else {
        response(503, "Unable to create the booking");
    }
}
catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}