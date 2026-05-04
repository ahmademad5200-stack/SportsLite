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
    if(!isset($input->title) || !isset($input->weeks_count) || !isset($input->sessions_per_week)){
        response(400, "Bad request: 'title', 'weeks_count', and 'sessions_per_week' are required");
    }

    $title = $input->title;
    $weeks = $input->weeks_count;
    $sessions = $input->sessions_per_week;
    $exercises = isset($input->assigned_exercises) ? $input->assigned_exercises : null;

    $query = "INSERT INTO training_programs (title, weeks_count, sessions_per_week, assigned_exercises) 
              VALUES (:title, :weeks, :sessions, :exercises)";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':weeks', $weeks, PDO::PARAM_INT);
    $stmt->bindParam(':sessions', $sessions, PDO::PARAM_INT);
    $stmt->bindParam(':exercises', $exercises);

    if($stmt->execute()){
        response(200, "Training program created successfully.", ["id" => $pdo->lastInsertId()]);
    } else {
        response(503, "Unable to create training program");
    }
} catch (Exception $e) {
    response(500, "Server error: " . $e->getMessage());
}