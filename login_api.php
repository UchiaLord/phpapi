<?php
session_start();

header('Content-Type: application/json');

require_once "./crud/db_connect.inc.php";
require_once "./crud/functions.php";

$response = array();

try {
    // Retrieve JSON input
    $json_data = file_get_contents("php://input");
    $data = json_decode($json_data, true);

    if (isset($data['email']) && isset($data['password'])) {
        $email = cleanInput($data['email']);
        $password = cleanInput($data['password']);

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response["success"] = false;
            $response["message"] = "Please enter a valid email address";
        } else {
            // Hash password
            $hashed_password = hash("sha256", $password);

            // Query database
            $sql = "SELECT * FROM users WHERE email = ? AND password = ?";
            $params = [$email, $hashed_password];
            $types = "ss";

            $result = dbq($sql, $params, $types);

            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);
                if ($row["status"] == "adm") {
                    $_SESSION["admin"] = $row["id"];
                    $response["success"] = true;
                    $response["message"] = "Login successful as admin";
                    $response["redirect"] = "crud/index.html";
                } else {
                    $_SESSION["user"] = $row["id"];
                    $response["success"] = true;
                    $response["message"] = "Login successful as user";
                    $response["redirect"] = "index.html";
                }
            } else {
                $response["success"] = false;
                $response["message"] = "Invalid credentials";
            }
        }
    } else {
        $response["success"] = false;
        $response["message"] = "Email and password are required";
    }

    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => $e->getMessage()]);
}
?>
