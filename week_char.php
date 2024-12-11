<?php
include('config.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

header("Content-Type: image/png");

$width = 700;
$height = 400;

$image = imagecreate($width, $height);

$backgroun_color = imagecolorallocate($image, 255, 255, 255);