<?php
require "db_connect.inc.php";
require "functions.php";

header('Content-Type: application/json');

try {
    if (!isset($_GET['id'])) {
        throw new Exception('Product ID not provided');
    }

    $productId = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if ($productId === false || $productId <= 0) {
        throw new Exception('Invalid product ID');
    }

    $query = "DELETE FROM `products` WHERE id = ?";
    $params = [$productId];
    $types = "i";

    $result = dbq($query, $params, $types);

    file_put_contents("log.txt", $result, FILE_APPEND);

   
    echo json_encode(['message' => 'Product deleted successfully', 'deleted_id' => $productId]);
    
} catch (Exception $e) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => $e->getMessage()]);
}
