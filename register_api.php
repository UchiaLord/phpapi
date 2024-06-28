<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
header('Content-Type: application/json');

require_once "./crud/db_connect.inc.php";
require_once "./crud/functions.php";

$response = [];

try {
    // Check if the required fields are set
    if (!isset($_POST["first_name"]) || !isset($_POST["last_name"]) || !isset($_POST["email"]) || !isset($_POST["password"]) || !isset($_POST["date_of_birth"])) {
        throw new Exception("Missing required parameters");
    }

    $first_name = cleanInput($_POST["first_name"]);
    $last_name = cleanInput($_POST["last_name"]);
    $email = cleanInput($_POST["email"]);
    $password = cleanInput($_POST["password"]);
    $date_of_birth = cleanInput($_POST["date_of_birth"]);

    // Optional image upload
    $picture = '';
    if (isset($_FILES["picture"]) && $_FILES["picture"]["error"] === UPLOAD_ERR_OK) {
        $picture = fileUpload($_FILES["picture"]);
        $picture = $picture[0]; // Assuming fileUpload returns an array with the first element as the filename
    }

    // Validation
    if (empty($first_name)) {
        throw new Exception("You can't leave the first name empty");
    } elseif (strlen($first_name) < 3) {
        throw new Exception("First name must be at least 3 chars");
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $first_name)) {
        throw new Exception("First name must contain only letters and spaces");
    }

    if (empty($last_name)) {
        throw new Exception("You can't leave the last name empty");
    } elseif (strlen($last_name) < 3) {
        throw new Exception("Last name must be at least 3 chars");
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $last_name)) {
        throw new Exception("Last name must contain only letters and spaces");
    }

    if (empty($email)) {
        throw new Exception("Email can't be empty");
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Please, enter a valid email");
    } else {
        $sql = "SELECT email FROM `users` WHERE email = ?";
        $result = dbq($sql, [$email], 's');
        if (mysqli_num_rows($result) != 0) {
            throw new Exception("Email already exists!");
        }
    }

    if (empty($password)) {
        throw new Exception("You can't leave the password empty");
    } elseif (strlen($password) < 6) {
        throw new Exception("Password must be at least 6 chars");
    }

    if (empty($date_of_birth)) {
        throw new Exception("Please select the date of birth");
    }

    // Hash the password
    $hashed_password = hash("sha256", $password);

    // Insert user into the database
    $insertQuery = "INSERT INTO `users`(`first_name`, `last_name`, `password`, `date_of_birth`, `email`, `picture`) VALUES (?, ?, ?, ?, ?, ?)";
    $params = [$first_name, $last_name, $hashed_password, $date_of_birth, $email, $picture];
    $types = "ssssss";

    $result = dbq($insertQuery, $params, $types);

    $logData = json_encode($result, JSON_PRETTY_PRINT);
    error_log(print_r($result, true)); // Überprüfen der Daten
    file_put_contents('log2.txt', $logData . PHP_EOL, FILE_APPEND);


    mail($email, "User created", "Your message to the user who created an account");
    $response["success"] = true;
    $response["message"] = "Registration successful";
    
} catch (Exception $e) {
    http_response_code(400);
    $response["success"] = false;
    $response["message"] = $e->getMessage();
}

echo json_encode($response);
