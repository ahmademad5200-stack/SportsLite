<?php
require_once "../Helpers/headers.php";
send_json_api_headers('POST'); // استلام البيانات عبر POST

require_once "../config/conn.php";
require_once "../Helpers/response.php";

// استقبال البيانات المرسلة (JSON)
$input = json_decode(file_get_contents("php://input"));

try {
    // 1. التحقق من البيانات الأساسية
    if (empty($input->full_name) || empty($input->email) || empty($input->password)) {
        response(400, "Full name, Email, and Password are required");
    }

    // 2. تشفير كلمة المرور (أهم خطوة للأمان)
    $hashed_password = password_hash($input->password, PASSWORD_BCRYPT);

    // 3. التأكد من أن الإيميل غير مستخدم مسبقاً
    $checkEmail = $pdo->prepare("SELECT id FROM trainees WHERE email = ?");
    $checkEmail->execute([$input->email]);
    if ($checkEmail->rowCount() > 0) {
        response(409, "Email is already registered");
    }

    // 4. جملة الإدخال (INSERT) متوافقة مع أعمدة جدولك
    $sql = "INSERT INTO trainees (name, email, phone, password_hash, gender, account_status) 
            VALUES (:name, :email, :phone, :pass, :gender, :status)";
    
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        ':name'   => $input->full_name,
        ':email'  => $input->email,
        ':phone'  => $input->phone ?? null,
        ':pass'   => $hashed_password,
        ':gender' => $input->gender ?? 'Male',
        ':status' => 'Active' // حالة الحساب الافتراضية
    ]);

    response(201, "Account created successfully", ["id" => $pdo->lastInsertId()]);

} catch (PDOException $e) {
    response(500, "Server error: " . $e->getMessage());
}