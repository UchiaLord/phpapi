<?php
require "db_connect.inc.php";

header('Content-Type: application/json');

try {
    $query = "SELECT `id`, `name`, `description`, `price`, `articlenumber`, `image_url` FROM `products`";
    $result = dbq($query);

    if ($result->num_rows > 0) {
        $products = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
        echo json_encode($products);
    } else {
        throw new Exception('No products found');
    }
} catch (Exception $e) {
    http_response_code(404); // Not Found
    echo json_encode(['error' => $e->getMessage()]);
}
