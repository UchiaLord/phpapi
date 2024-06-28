<?php
session_start();

header('Content-Type: application/json');

$response = array();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        unset($_SESSION["admin"]);
        unset($_SESSION["user"]);
        session_unset();
        session_destroy();

        $response["success"] = true;
        $response["message"] = "Logout successful";
    } else {
        http_response_code(405); // Method Not Allowed
        $response["success"] = false;
        $response["message"] = "Invalid request method";
    }
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $response["success"] = false;
    $response["message"] = "An error occurred: " . $e->getMessage();
}

echo json_encode($response);
