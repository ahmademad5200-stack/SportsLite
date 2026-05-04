<?php
require_once "../Helpers/headers.php";
send_json_api_headers('POST');
require_once "../Helpers/response.php";

// مستقبلاً هون بنحط كود فحص الـ JWT Token
response(200, "Token is valid");