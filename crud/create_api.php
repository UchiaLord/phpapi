<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "./db_connect.inc.php";
require "./functions.php";
require "./file_upload.php";

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Validate received data
    if (!isset($_POST['name']) || !isset($_POST['description']) || !isset($_POST['price']) || !isset($_POST['articlenumber'])) {
        throw new Exception('Invalid input');
    }

    // Clean and validate inputs
    $name = cleanInput($_POST['name']);
    $description = cleanInput($_POST['description']);
    $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
    $articlenumber = cleanInput($_POST['articlenumber']);
    $image_url = isset($_FILES['image_url']) ? fileUpload($_FILES['image_url'], "products") : null;

    if ($price === false) {
        throw new Exception('Invalid price');
    }

    // Prepare and execute SQL query
    $query = "INSERT INTO `products`(`name`, `description`, `price`, `articlenumber`, `image_url`) VALUES (?, ?, ?, ?, ?)";
    $params = [$name, $description, $price, $articlenumber, $image_url[0]];
    $types = "ssdss"; // Assuming `price` is decimal (d) and `articlenumber` is string (s)

    $last_insert_id = dbq($query, $params, $types);
    file_put_contents("log.txt", $last_insert_id, FILE_APPEND);

    // Respond with JSON
    echo json_encode(["message" => "Product added successfully", "inserted_id" => $last_insert_id]);
} catch (Exception $e) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => $e->getMessage()]);
}
