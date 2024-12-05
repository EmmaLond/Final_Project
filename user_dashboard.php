<?php
include('config.php');
include('auth.php');

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
    $hours = $_POST['hours'];  // Added for hours input
    $minutes = $_POST['minutes'];  // Added for minutes input
    $seconds = $_POST['seconds'];
    $notes = $_POST['notes'];
    $user_id = $_SESSION['user_id']; // Get the user ID from the session

    // Convert the time to total seconds
    $time = ($hours * 3600) + ($minutes * 60) + $seconds;

    // Insert activity into the stats table
    $sql = "INSERT INTO stats (user_id, title, mileage, time, notes, date) 
            VALUES (:user_id, :title, :mileage, :time, :notes, NOW())";

    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':mileage', $mileage, PDO::PARAM_STR);
        $stmt->bindParam(':time', $time, PDO::PARAM_INT);
        $stmt->bindParam(':notes', $notes, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "Activity added successfully!";
        } else {
            echo "Something went wrong. Please try again.";
        }
    }
}


// Fetch recent activity stats for the current week
$user_id = $_SESSION['user_id'];
$start_of_week = date('Y-m-d', strtotime('monday this week'));
$end_of_week = date('Y-m-d', strtotime('sunday this week'));

$sql = "SELECT COUNT(*) AS activity_count, 
               SUM(mileage) AS total_mileage, 
               SUM(time) AS total_time 
        FROM stats 
        WHERE user_id = :user_id AND date BETWEEN :start_date AND :end_date";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':start_date', $start_of_week, PDO::PARAM_STR);
$stmt->bindParam(':end_date', $end_of_week, PDO::PARAM_STR);
$stmt->execute();
$weekly_stats = $stmt->fetch(PDO::FETCH_ASSOC);


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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="stylesF.css">
</head>
<body>
    <header>
        <nav>
            <a href="index.php">Home</a> | 
            <a href="user_dashboard.php">My Dashboard</a> | 
            <a href="login.php">Logout</a>
        </nav>
    </header>

<main>
    <h1>Welcome to Your Dashboard</h1>

    <section id= "recent-activity">
        <h2>This Week's Stats</h2>
        <?php if ($weekly_stats['activity_count'] > 0): ?>
            <p>Total Activities: <?php echo $weekly_stats['activity_count']; ?></p>
            <p>Total Mileage: <?php echo round($weekly_stats['total_mileage'], 2); ?> mi</p>
            <p>
                Total Time: 
                <?php 
                    $hours = floor($weekly_stats['total_time'] / 3600);
                    $minutes = floor(($weekly_stats['total_time'] % 3600) / 60);
                    $seconds = $weekly_stats['total_time'] % 60;
                    echo "{$hours}h {$minutes}m {$seconds}s";
                    ?>
                </p>
            <?php else: ?>
                <p>No activities logged this week.</p>
            <?php endif; ?>
    </section>






    <form method="POST" action="user_dashboard.php">
        <input type="text" name="search_title" placeholder="Search by activity title"  value="<?php echo htmlspecialchars($search_title); ?>">
        <button type="submit" name="search">Search</button>
    </form>

    <hr>

    <h2>Add New Activity</h2>
    <form method="POST" action="user_dashboard.php">
        <input type="text" name="title" placeholder="Activity Title" required><br>
        <input type="number" step="0.1" name="mileage" placeholder="Mileage (mi)" required><br>

        <!-- Time input fields for hours, minutes, and seconds -->
        <label for="hours">Hours:</label>
        <input type="number" name="hours" min="0" max="24" placeholder="Hours" required><br>

        <label for="minutes">Minutes:</label>
        <input type="number" name="minutes" min="0" max="59" placeholder="Minutes" required><br>
        <label for="seconds">Seconds:</label>
        <input type="number" name="seconds" min="0" max="59" placeholder="Seconds" required><br>

        <textarea name="notes" placeholder="Notes" ></textarea><br>
        <button type="submit" name="add_activity">Add Activity</button>
</form>

<hr>

<h2>Past Activities</h2>
<?php if (count($activities) > 0): ?>
    <table>
        <thead>
            <tr>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Mileage</th>
                        <th>Time</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activities as $activity): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($activity['title']); ?></td>
                            <td><?php echo htmlspecialchars($activity['date']); ?></td>
                            <td><?php echo htmlspecialchars($activity['mileage']); ?> km</td>
                            <td>
                                <?php 
                                    $hours = floor($activity['time'] / 3600);
                                    $minutes = floor(($activity['time'] % 3600) / 60);
                                    $seconds = $activity['time'] % 60;
                                    echo "{$hours}h {$minutes}m {$seconds}s";
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($activity['notes']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No activities found.</p>
        <?php endif; ?>

</main>
</body>
</html>