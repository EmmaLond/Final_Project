<?php
include('config.php');

session_start();

//Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit;
}

// Handle adding a new activity
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_activity'])) {
    $title = $_POST['title'];
    $mileage = $_POST['mileage'];
    $time = $_POST['time'];
    $notes = $_POST['notes'];
    $user_id = $_SESSION['user_id']; // Get the user ID from the session

    // Insert activity into the stats table
    $sql = "INSERT INTO stats (user_id, title, mileage, time, notes, date) 
            VALUES (:user_id, :title, :mileage, :time, :notes, NOW())";
    
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':mileage', $mileage, PDO::PARAM_STR);
        $stmt->bindParam(':time', $time, PDO::PARAM_STR);
        $stmt->bindParam(':notes', $notes, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "Activity added successfully!";
        } else {
            echo "Something went wrong. Please try again.";
        }
    }
}

// Handle search functionality
$search_title = '';
if (isset($_POST['search'])) {
    $search_title = $_POST['search_title'];
    $sql = "SELECT * FROM stats WHERE user_id = :user_id AND title LIKE :title ORDER BY date DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(':title', "%" . $search_title . "%", PDO::PARAM_STR);
} else {
    $sql = "SELECT * FROM stats WHERE user_id = :user_id ORDER BY date DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
}
$stmt->execute();
$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
