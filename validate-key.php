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

$key_data = null;
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $license_key = strtoupper(trim($_POST['license_key'] ?? ''));
    
    $stmt = $conn->prepare('SELECT k.*, m.name as mod_name, u.username FROM license_keys k LEFT JOIN mods m ON k.mod_id = m.id LEFT JOIN users u ON k.user_id = u.id WHERE k.license_key = ?');
    $stmt->bind_param('s', $license_key);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $key_data = $result->fetch_assoc();
        
        $now = new DateTime();
        $expires = new DateTime($key_data['expiration_date']);
        $key_data['is_expired'] = $now > $expires;
        $key_data['is_valid'] = $key_data['status'] === 'active' && !$key_data['is_expired'];
        $key_data['days_remaining'] = max(0, $expires->diff($now)->days);
    } else {
        $error = "License key not found";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validate License Key - Modding Panel</title>
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
                <li><a href="generate-key.php">Generate Key</a></li>
                <li><a href="validate-key.php" class="active">Validate Key</a></li>
                <li><a href="settings.php">Settings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
        
        <div class="main-content">
            <header>
                <h1>Validate License Key</h1>
            </header>
            
            <div class="content">
                <div class="section">
                    <form method="POST" class="validate-form">
                        <div class="form-group">
                            <label for="license_key">Enter License Key</label>
                            <input type="text" id="license_key" name="license_key" placeholder="XXXX-XXXX-XXXX-XXXX" required style="text-transform: uppercase;">
                        </div>
                        <button type="submit" class="btn-primary">Validate Key</button>
                    </form>
                </div>
                
                <?php if(isset($error)): ?>
                    <div class="section">
                        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                    </div>
                <?php endif; ?>
                
                <?php if($key_data): ?>
                    <div class="section">
                        <h2>Key Validation Result</h2>
                        
                        <div class="validation-result <?php echo $key_data['is_valid'] ? 'valid' : 'invalid'; ?>">
                            <div class="result-icon">
                                <?php echo $key_data['is_valid'] ? '✓' : '✗'; ?>
                            </div>
                            <h3><?php echo $key_data['is_valid'] ? 'Valid License' : 'Invalid License'; ?></h3>
                        </div>
                        
                        <div class="key-details">
                            <div class="detail-row">
                                <span class="label">License Key:</span>
                                <span class="value"><code><?php echo htmlspecialchars($key_data['license_key']); ?></code></span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Status:</span>
                                <span class="value">
                                    <span class="status-badge status-<?php echo $key_data['is_valid'] ? 'active' : ($key_data['is_expired'] ? 'expired' : 'inactive'); ?>">
                                        <?php echo $key_data['is_valid'] ? 'Active' : ($key_data['is_expired'] ? 'Expired' : ucfirst($key_data['status'])); ?>
                                    </span>
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Mod:</span>
                                <span class="value"><?php echo htmlspecialchars($key_data['mod_name'] ?? 'All Mods'); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="label">User:</span>
                                <span class="value"><?php echo htmlspecialchars($key_data['username'] ?? 'Not Assigned'); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Duration:</span>
                                <span class="value"><?php echo $key_data['duration_days']; ?> days</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Created:</span>
                                <span class="value"><?php echo date('M d, Y H:i', strtotime($key_data['created_at'])); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Expires:</span>
                                <span class="value"><?php echo date('M d, Y H:i', strtotime($key_data['expiration_date'])); ?></span>
                            </div>
                            <?php if(!$key_data['is_expired']): ?>
                            <div class="detail-row">
                                <span class="label">Days Remaining:</span>
                                <span class="value"><strong><?php echo $key_data['days_remaining']; ?> days</strong></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>