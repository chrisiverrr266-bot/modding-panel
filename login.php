<?php
session_start();
require_once 'config.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Database connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }
    
    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare('SELECT id, username, password FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if(password_verify($password, $user['password'])) {
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            header('Location: dashboard.php');
            exit();
        } else {
            header('Location: index.php?error=Invalid credentials');
            exit();
        }
    } else {
        header('Location: index.php?error=Invalid credentials');
        exit();
    }
    
    $stmt->close();
    $conn->close();
} else {
    header('Location: index.php');
    exit();
}
?>