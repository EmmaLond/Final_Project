<?php
// login.php - Login page
session_start();
require_once 'config.php';
require_once 'auth.php';

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        if (login_user($pdo, $username, $password)) {
            header('Location: index5.php');
            exit;
        } else {
            $error_message = 'Invalid username or password';
        }
    }
}
?>

<!DOCTYPE html>
<html lang = "en">
<head>
    <meta charset= "UTF-8">
    <title> Login - InStride </title>
    <link rel = "stylesheet" href= "styles.css">
</head>
<body>
    <div class= "auth-container">
        <h1>Login Here</h1>
        
    </div>
</body>