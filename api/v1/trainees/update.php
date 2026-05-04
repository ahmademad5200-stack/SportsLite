<?php
require_once "../Helpers/headers.php";
send_json_api_headers('PUT'); // تم التعديل لتتناسب مع .htaccess

require_once "../config/conn.php";
require_once "../Helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "PUT") {
    response(405, "Only PUT Method is allowed");
}

$input = json_decode(file_get_contents("php://input"));

try {
    if (!isset($input->id)) {
        response(400, "Bad request: 'id' is required");
    }

    $id = $input->id;
    
    if (!is_numeric($id)) {
        response(400, "Bad request: 'id' must be numeric.");
    }

    // التحقق من وجود المتدرب
    $check = $pdo->prepare("SELECT * FROM trainees WHERE id = :id");
    $check->bindParam(":id", $id, PDO::PARAM_INT);
    $check->execute();
    
    if ($check->rowCount() === 0) {
        response(404, "Bad request: Trainee not found!");
    }
    
    $existing_data = $check->fetch(PDO::FETCH_ASSOC);

    // تحديث القيم الجديدة أو الاحتفاظ بالقيم القديمة إذا لم يتم إرسالها
    $name = isset($input->name) ? $input->name : $existing_data['name'];
    $email = isset($input->email) ? $input->email : $existing_data['email'];
    $phone = isset($input->phone) ? $input->phone : $existing_data['phone'];
    $account_status = isset($input->account_status) ? $input->account_status : $existing_data['account_status'];
    $birth_date = isset($input->birth_date) ? $input->birth_date : $existing_data['birth_date'];
    $gender = isset($input->gender) ? $input->gender : $existing_data['gender'];

    $query = "UPDATE trainees SET name = :name, email = :email, phone = :phone, account_status = :account_status, birth_date = :birth_date, gender = :gender WHERE id = :id";
    $stmt = $pdo->prepare($query);
    
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->bindParam(":name", $name, PDO::PARAM_STR);
    $stmt->bindParam(":email", $email, PDO::PARAM_STR);
    $stmt->bindParam(":phone", $phone, PDO::PARAM_STR);
    $stmt->bindParam(":account_status", $account_status, PDO::PARAM_STR);
    $stmt->bindParam(":birth_date", $birth_date);
    $stmt->bindParam(":gender", $gender, PDO::PARAM_STR);

    if ($stmt->execute()) {
        response(200, "Trainee updated successfully.");
    } else {
        response(503, "Unable to update trainee");
    }
} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
}