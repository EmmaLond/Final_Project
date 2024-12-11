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

// Draw axes
$margin = 50;
imageline($image, $margin, $height - $margin, $width - $margin, $height - $margin, $axis_color); // X-axis
imageline($image, $margin, $margin, $margin, $height - $margin, $axis_color); // Y-axis

// Fetch data for the current week
$user_id = $_SESSION['user_id'];
$start_of_week = date('Y-m-d', strtotime('monday this week'));
$end_of_week = date('Y-m-d', strtotime('sunday this week'));

$sql = "SELECT DATE(date) as activity_date, SUM(mileage) as total_mileage 
        FROM stats 
        WHERE user_id = :user_id AND date BETWEEN :start_date AND :end_date 
        GROUP BY DATE(date)";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':start_date', $start_of_week, PDO::PARAM_STR);
$stmt->bindParam(':end_date', $end_of_week, PDO::PARAM_STR);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare data
$days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
$mileage_per_day = array_fill(0, 7, 0); // Initialize with zero

foreach ($data as $row) {
    $day_index = date('N', strtotime($row['activity_date'])) - 1; // Convert date to weekday index
    $mileage_per_day[$day_index] = $row['total_mileage'];
}

// Draw bars
$max_mileage = max($mileage_per_day) ?: 1; // Avoid division by zero
$bar_width = (int)(($width - 2 * $margin) / count($days)) - 10;

for ($i = 0; $i < count($days); $i++) {
    $x1 = $margin + ($i * ($bar_width + 10));
    $x2 = $x1 + $bar_width;
    $y1 = $height - $margin - ($mileage_per_day[$i] / $max_mileage * ($height - 2 * $margin));
    $y2 = $height - $margin;

    imagefilledrectangle($image, $x1, $y1, $x2, $y2, $bar_color);

    // Add labels
    imagestring($image, 3, $x1 + 5, $height - $margin + 5, $days[$i], $text_color);
    imagestring($image, 3, $x1 + 5, $y1 - 15, round($mileage_per_day[$i], 1), $text_color);
}

// Output the image
imagepng($image);
imagedestroy($image);
?>