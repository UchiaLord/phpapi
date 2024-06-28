<?php
function dbq($query, $params = [], $types = "")
{
    $database = "api_crud";
    $hostname = "127.0.0.1";
    $password = "";
    $username = "root";

    // Verbindung zur Datenbank herstellen
    $connect = mysqli_connect($hostname, $username, $password, $database);



    // Überprüfen, ob die Verbindung erfolgreich war
    if (!$connect) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Prepare statement mit parameterized query
    $stmt = mysqli_prepare($connect, $query);

    // Überprüfen, ob das statement korrekt vorbereitet wurde
    if ($stmt === false) {
        die("Query preparation failed: " . mysqli_error($connect));
    }

    // Bind parameters, falls Parameter vorhanden sind
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    // Query ausführen
    $success = mysqli_stmt_execute($stmt);
    
    // Ergebnis des Execute-Vorgangs überprüfen
    if ($success) {
        $result = mysqli_stmt_get_result($stmt); 
        // Get mysqli_result object
        mysqli_stmt_close($stmt);
        mysqli_close($connect);
        return $result; // Rückgabe des mysqli_result Objekts
    } else {
        // Fehler beim Ausführen des Statements
        die("Query execution failed: " . mysqli_stmt_error($stmt));
    }
    
}
