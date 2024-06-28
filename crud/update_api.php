<?php
require "./db_connect.inc.php";
require "./file_upload.php";
require "./functions.php";

header('Content-Type: application/json');

try {
    // Überprüfe, ob alle benötigten Daten übergeben wurden
    if (!isset($_POST['id']) || !isset($_POST['name']) || !isset($_POST['description']) || !isset($_POST['price']) || !isset($_POST['articlenumber'])) {
        throw new Exception('Invalid input');
    }

    // Eingaben validieren und bereinigen
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
    $name = cleanInput($_POST['name']);
    $description = cleanInput($_POST['description']);
    $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
    $articlenumber = cleanInput($_POST['articlenumber']);
    $image_url = isset($_POST['image_url']) ? cleanInput($_POST['image_url']) : '';

    // Debugging-Ausgaben
    error_log("ID: $id");
    error_log("Name: $name");
    error_log("Description: $description");
    error_log("Price: $price");
    error_log("Article Number: $articlenumber");
    error_log("Image URL: $image_url");

    if ($id === false || $price === false) {
        throw new Exception('Invalid ID or price');
    }

    // SQL-Abfrage
    $query = "UPDATE `products` SET `name`=?, `description`=?, `price`=?, `articlenumber`=?, `image_url`=? WHERE `id`=?";
    $params = [$name, $description, $price, $articlenumber, $image_url, $id];
    $types = "ssdisi";

    // Abfrage ausführen
    $result = dbq($query, $params, $types);

    // Überprüfe, ob die Aktualisierung erfolgreich war

    echo json_encode(["message" => "Product updated successfully"]);
} catch (Exception $e) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => $e->getMessage()]);
}
