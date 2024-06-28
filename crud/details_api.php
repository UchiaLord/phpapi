<?php
require "db_connect.inc.php";

header('Content-Type: application/json');

try {
    // Überprüfen, ob die ID gesetzt und gültig ist
    if (!isset($_GET['id'])) {
        throw new Exception('Product ID not provided');
    }

    $productId = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if ($productId === false || $productId <= 0) {
        throw new Exception('Invalid product ID');
    }

    // SQL-Abfrage mit Platzhalter für Parameter
    $query = "SELECT `id`, `name`, `description`, `price`, `articlenumber`, `image_url` FROM `products` WHERE id = ?";
    $params = [$productId];
    $types = "i";

    // Ausführen der Abfrage
    $result = dbq($query, $params, $types);

    // Überprüfen, ob Ergebnisse zurückgegeben wurden
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        echo json_encode($product); // Einzelnes Produkt zurückgeben
    } else {
        http_response_code(404); // Not Found
        echo json_encode(['error' => 'Product not found']);
    }

} catch (Exception $e) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => $e->getMessage()]);
}
?>
