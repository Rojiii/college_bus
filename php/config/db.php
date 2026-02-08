<?php


try {
    $conn = new PDO(
        "mysql:host=localhost;dbname=college_bus_routing",
        "root",
        ""
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed");
}
