<?php
session_start();
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modding Panel - Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="dashboard">
        <nav class="sidebar">
            <div class="logo">
                <h2>Modding Panel</h2>
            </div>
            <ul class="menu">
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="mods.php">Mods</a></li>
                <li><a href="search.php">Search Mods</a></li>
                <li><a href="upload.php">Upload Mod</a></li>
                <li><a href="settings.php">Settings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
        
        <div class="main-content">
            <header>
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            </header>
            
            <div class="content">
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Total Mods</h3>
                        <p class="stat-number">0</p>
                    </div>
                    <div class="stat-card">
                        <h3>Downloads</h3>
                        <p class="stat-number">0</p>
                    </div>
                    <div class="stat-card">
                        <h3>Active Users</h3>
                        <p class="stat-number">1</p>
                    </div>
                </div>
                
                <div class="section">
                    <h2>Recent Activity</h2>
                    <p>No recent activity.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>