<?php
require_once "../Helpers/headers.php";
// نغير الميثود المسموحة هنا إلى DELETE
send_json_api_headers('DELETE');

require_once "../config/conn.php";
require_once "../Helpers/response.php";

// رفض أي طلب نوع اتصاله ليس DELETE
if ($_SERVER["REQUEST_METHOD"] !== "DELETE") {
    response(405, "Only DELETE Method is allowed");
}

// استقبال البيانات (الـ id) من جسم الطلب
$input = json_decode(file_get_contents("php://input"));

try {
    // التأكد أن الـ id موجود في الطلب
    if (!isset($input->id)) {
        response(400, "Bad request: 'id' is required for deletion");
    }

    $id = $input->id;

    // التأكد أن القيمة رقمية
    if (!is_numeric($id)) {
        response(400, "Bad request: id must be numeric.");
    }

    // 1. التأكد أولاً من وجود الخطة قبل محاولة حذفها (Existence Check)
    $select_query = "SELECT `id` FROM `subscription_plans` WHERE `id` = :id";
    $check = $pdo->prepare($select_query);
    $check->bindParam(":id", $id, PDO::PARAM_INT);
    $check->execute();

    if ($check->rowCount() === 0) {
        response(404, "Bad request: Plan not found!");
    }

    // 2. تنفيذ عملية الحذف
    $delete_query = "DELETE FROM `subscription_plans` WHERE `id` = :id";
    $delete_stmnt = $pdo->prepare($delete_query);
    $delete_stmnt->bindParam(":id", $id, PDO::PARAM_INT);

    if ($delete_stmnt->execute()) {
        response(200, "Plan deleted successfully.");
    } else {
        response(503, "Unable to delete the plan");
    }

} catch (Exception $e) {
    response(500, "server error: " . $e->getMessage());
} catch (PDOException $e) {
    response(500, "Database error: " . $e->getMessage());
}