<?php
require_once "../Helpers/headers.php";
send_json_api_headers('POST'); // تسجيل الدخول دائماً POST لحماية البيانات

require_once "../config/conn.php";
require_once "../Helpers/response.php";

$input = json_decode(file_get_contents("php://input"));

try {
    // 1. التأكد من إدخال الإيميل والباسورد
    if (empty($input->email) || empty($input->password)) {
        response(400, "Email and Password are required");
    }

    // 2. البحث عن المستخدم عن طريق الإيميل
    $stmt = $pdo->prepare("SELECT * FROM trainees WHERE email = ?");
    $stmt->execute([$input->email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 3. التحقق من وجود المستخدم ومطابقة الباسورد المشفر
    if ($user && password_verify($input->password, $user['password_hash'])) {
        
        // منع إرجاع الباسورد المشفر في الجيسون لزيادة الأمان
        unset($user['password_hash']);

        // 4. الرد بنجاح وإرجاع بيانات المستخدم (مهمة للـ Frontend)
        response(200, "Login successful", [
            "user" => $user
        ]);

    } else {
        // إذا الإيميل غلط أو الباسورد غلط، بنعطي رسالة موحدة للأمان
        response(401, "Invalid email or password");
    }

} catch (PDOException $e) {
    response(500, "Server error: " . $e->getMessage());
}