<?php
require_once "../Helpers/headers.php";
send_json_api_headers('POST');
require_once "../Helpers/response.php";

// في الـ APIs، الفرونت-إند هو اللي بيمسح الـ Token أو الـ User Data
// بس بنعمل هاد الملف عشان إذا حبيت تسجل "وقت تسجيل الخروج" بالقاعدة مستقبلاً
response(200, "Logged out successfully");