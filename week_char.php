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

$background_color = imagecolorallocate($image, 255, 255, 255);
$axis_color = imagecolorallocate($image, 0, 0, 0);          
$bar_color = imagecolorallocate($image, 137, 31, 253);         
$text_color = imagecolorallocate($image, 0, 0, 0); 