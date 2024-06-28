<?php
// Function to clean user input to prevent XSS and other attacks
function cleanInput($var)
{
    $data = trim($var);                 // Remove extra spaces from the beginning and end
    $data = strip_tags($data);          // Remove HTML and PHP tags
    $data = htmlspecialchars($data);    // Convert special characters to HTML entities

    return $data;
}

// Function to execute a secure database query with prepared statements
function executeQuery($connect, $query, $params, $types)
{
    // Prepare the SQL statement
    $stmt = $connect->prepare($query);

    if ($stmt === false) {
        throw new Exception('Query preparation failed: ' . htmlspecialchars($connect->error));
    }

    // Bind parameters to the SQL statement
    if (!empty($params) && !empty($types)) {
        $stmt->bind_param($types, ...$params);
    }

    // Execute the SQL statement
    $stmt->execute();

    // Get the result, if any
    $result = $stmt->get_result();

    // Close the statement
    $stmt->close();

    return $result;
}
