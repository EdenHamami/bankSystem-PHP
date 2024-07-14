<?php
require_once '../config/database.php';

$database = Database::getInstance();
$db = $database->getConnection();

try {
    $stmt = $db->query("SELECT 1");
    if ($stmt) {
        echo "Connection successful!";
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
