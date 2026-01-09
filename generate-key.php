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

// Handle key generation
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mod_id = $_POST['mod_id'] ?? null;
    $user_id = $_POST['user_id'] ?? null;
    $duration = (int)($_POST['duration'] ?? 30);
    
    // Generate unique license key
    $license_key = strtoupper(bin2hex(random_bytes(8)));
    $license_key = substr($license_key, 0, 4) . '-' . substr($license_key, 4, 4) . '-' . substr($license_key, 8, 4) . '-' . substr($license_key, 12, 4);
    
    $expiration_date = date('Y-m-d H:i:s', strtotime("+{$duration} days"));
    
    $stmt = $conn->prepare('INSERT INTO license_keys (license_key, mod_id, user_id, duration_days, expiration_date, status) VALUES (?, ?, ?, ?, ?, "active")');
    $stmt->bind_param('siiis', $license_key, $mod_id, $user_id, $duration, $expiration_date);
    
    if($stmt->execute()) {
        $success = "License key generated successfully: {$license_key}";
    } else {
        $error = "Failed to generate key: " . $conn->error;
    }
    $stmt->close();
}

// Get mods and users for dropdown
$mods = $conn->query('SELECT id, name FROM mods');
$users = $conn->query('SELECT id, username FROM users');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate License Key - Modding Panel</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="dashboard">
        <nav class="sidebar">
            <div class="logo">
                <h2>Modding Panel</h2>
            </div>
            <ul class="menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="mods.php">Mods</a></li>
                <li><a href="keys.php">License Keys</a></li>
                <li><a href="generate-key.php" class="active">Generate Key</a></li>
                <li><a href="validate-key.php">Validate Key</a></li>
                <li><a href="settings.php">Settings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
        
        <div class="main-content">
            <header>
                <h1>Generate License Key</h1>
            </header>
            
            <div class="content">
                <div class="section">
                    <?php if(isset($success)): ?>
                        <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>
                    <?php if(isset($error)): ?>
                        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" class="key-form">
                        <div class="form-group">
                            <label for="mod_id">Select Mod (Optional)</label>
                            <select id="mod_id" name="mod_id">
                                <option value="">-- All Mods --</option>
                                <?php while($mod = $mods->fetch_assoc()): ?>
                                    <option value="<?php echo $mod['id']; ?>"><?php echo htmlspecialchars($mod['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="user_id">Assign to User (Optional)</label>
                            <select id="user_id" name="user_id">
                                <option value="">-- No User --</option>
                                <?php while($user = $users->fetch_assoc()): ?>
                                    <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="duration">Duration (Days)</label>
                            <input type="number" id="duration" name="duration" value="30" min="1" max="3650" required>
                            <small>Enter number of days until expiration (1-3650 days)</small>
                        </div>
                        
                        <button type="submit" class="btn-primary">Generate License Key</button>
                    </form>
                </div>
                
                <div class="section">
                    <h2>Common Durations</h2>
                    <div class="duration-presets">
                        <button class="preset-btn" onclick="setDuration(7)">7 Days</button>
                        <button class="preset-btn" onclick="setDuration(30)">30 Days</button>
                        <button class="preset-btn" onclick="setDuration(90)">90 Days</button>
                        <button class="preset-btn" onclick="setDuration(365)">1 Year</button>
                        <button class="preset-btn" onclick="setDuration(730)">2 Years</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    function setDuration(days) {
        document.getElementById('duration').value = days;
    }
    </script>
</body>
</html>