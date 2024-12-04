<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if user is not logged in
    exit;
}
?>

<!--create html section of web app-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instride</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <nav>
            <a href="index.html">Home</a> | 
            <a href="user_dashboard.php">My Dashboard</a> | 
            <a href="about.html">About</a>
        </nav>
    </header>

    <main>

        <section class = "hero">
            <h1>InStride</h1>
            <h3>"Stride at Your Own Pace—Fitness, Without the Pressure."</h3>
        </section>

        <section class="about">
            <div class="section-content">
                <h2>About Us</h2>
                <p>We offer a refreshing approach to running. No comparisons, just progress</p>
            <button class="cta-btn">Learn More</button>
        </div>
        </section>

        <section class="user">
            <div class="section-content">
                <h2>View My Profile</h2>
                <a href="user_dashboard.php" class="cta-btn">View Profile</a>
            </div>
        </section>

        <section class="content">
            <div class="section-content">
                <h2>Explore Our Content</h2>
                <table>
                    <tr>
                        <td>
                            <h3>Find your Own Workout Plan</h3>
                            <p>Find your fitness routines that prioritize your well-being without comparison.</p>
                            <a href="https://www.muscleandstrength.com/workout-routines">Read More</a>
                        </td>
                        <td>
                            <h3>Mindful Running</h3>
                            <p>Explore the benefits of running with mindfulness and self-awareness.</p>
                            <a href="https://mindfulness.com/mindful-living/mindful-running">Read More</a>
                        </td>
                        <td>
                            <h3>Nutrition Tips for Beginners</h3>
                            <p>Basic nutrition advice to complement your fitness journey.</p>
                            <a href="https://www.healthline.com/nutrition/27-health-and-nutrition-tips">Read More</a>
                        </td>
                    </tr>
                </table>
            </div>
        </section>

        
    </main>
    <footer>
        <p>Contact me at: <a href="mailto:someone@example.com">someone@example.com</a></p>
    </footer>
</body>
</html>