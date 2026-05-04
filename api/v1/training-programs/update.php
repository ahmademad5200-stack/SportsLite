<?php
require_once "../Helpers/headers.php";
send_json_api_headers('PUT');

require_once "../config/conn.php";
require_once "../Helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "PUT") {
    response(405, "Only PUT Method is allowed");
}

$input = json_decode(file_get_contents("php://input"));

try {
    if (!isset($input->id) || !isset($input->title)) {
        response(400, "Bad request: 'id' and 'title' are required");
    }

    $id = $input->id;
    $title = $input->title;
    $weeks = $input->weeks_count;
    $sessions = $input->sessions_per_week;
    $exercises = $input->assigned_exercises;

    $check = $pdo->prepare("SELECT id FROM training_programs WHERE id = :id");
    $check->execute([':id' => $id]);
    if ($check->rowCount() === 0) response(404, "Program not found!");

    $query = "UPDATE training_programs SET title = :title, weeks_count = :weeks, sessions_per_week = :sessions, assigned_exercises = :exercises WHERE id = :id";
    $stmt = $pdo->prepare($query);
    
    $params = [
        ':title' => $title,
        ':weeks' => $weeks,
        ':sessions' => $sessions,
        ':exercises' => $exercises,
        ':id' => $id
    ];

    if ($stmt->execute($params)) {
        response(200, "Program updated successfully.");
    } else {
        response(503, "Unable to update program");
    }
} catch (Exception $e) {
    response(500, "Server error: " . $e->getMessage());
}