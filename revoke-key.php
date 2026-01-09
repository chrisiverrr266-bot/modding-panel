<?php
session_start();
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

require_once 'config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$key_id = (int)($_GET['id'] ?? 0);

if($key_id > 0) {
    $stmt = $conn->prepare('UPDATE license_keys SET status = "revoked" WHERE id = ?');
    $stmt->bind_param('i', $key_id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header('Location: keys.php');
exit();
?>