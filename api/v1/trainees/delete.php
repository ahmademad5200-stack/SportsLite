<?php
require_once "../Helpers/headers.php";
send_json_api_headers('DELETE');

require_once "../config/conn.php";
require_once "../Helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "DELETE") {
    response(405, "Only DELETE Method is allowed");
}

$input = json_decode(file_get_contents("php://input"));

try {
    if (!isset($input->id)) {
        response(400, "Bad request: 'id' is required for deletion");
    }

    $id = $input->id;

    if (!is_numeric($id)) {
        response(400, "Bad request: id must be numeric.");
    }

    $select_query = "SELECT id FROM trainees WHERE id = :id";
    $check = $pdo->prepare($select_query);
    $check->bindParam(":id", $id, PDO::PARAM_INT);
    $check->execute();

    if ($check->rowCount() === 0) {
        response(404, "Bad request: Trainee not found!");
    }

    $delete_query = "DELETE FROM trainees WHERE id = :id";
    $delete_stmnt = $pdo->prepare($delete_query);
    $delete_stmnt->bindParam(":id", $id, PDO::PARAM_INT);

    if ($delete_stmnt->execute()) {
        response(200, "Trainee deleted successfully.");
    } else {
        response(503, "Unable to delete trainee");
    }

} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
} catch (PDOException $e) {
    response(500, "Database error: " . $e->getMessage());
}