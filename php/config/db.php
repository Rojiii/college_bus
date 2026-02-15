<?php


try {
    
    $conn = new PDO(
        dsn: "mysql:host=localhost;dbname=college_bus",
        username: "root",
        password: ""
    );
    $conn->setAttribute(attribute: PDO::ATTR_ERRMODE, value: PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed");
}
