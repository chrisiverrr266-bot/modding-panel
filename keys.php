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

// Fetch all keys
$stmt = $conn->prepare('SELECT k.*, m.name as mod_name, u.username FROM license_keys k LEFT JOIN mods m ON k.mod_id = m.id LEFT JOIN users u ON k.user_id = u.id ORDER BY k.created_at DESC');
$stmt->execute();
$keys = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>License Keys - Modding Panel</title>
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
                <li><a href="keys.php" class="active">License Keys</a></li>
                <li><a href="generate-key.php">Generate Key</a></li>
                <li><a href="validate-key.php">Validate Key</a></li>
                <li><a href="settings.php">Settings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
        
        <div class="main-content">
            <header>
                <h1>License Keys Management</h1>
            </header>
            
            <div class="content">
                <div class="section">
                    <div class="section-header">
                        <h2>All License Keys</h2>
                        <a href="generate-key.php" class="btn-primary">Generate New Key</a>
                    </div>
                    
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Key</th>
                                <th>Mod</th>
                                <th>User</th>
                                <th>Duration</th>
                                <th>Expires</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($key = $keys->fetch_assoc()): 
                                $now = new DateTime();
                                $expires = new DateTime($key['expiration_date']);
                                $is_expired = $now > $expires;
                                $is_active = $key['status'] === 'active' && !$is_expired;
                            ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars($key['license_key']); ?></code></td>
                                <td><?php echo htmlspecialchars($key['mod_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($key['username'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($key['duration_days']); ?> days</td>
                                <td><?php echo date('M d, Y', strtotime($key['expiration_date'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $is_active ? 'active' : ($is_expired ? 'expired' : 'inactive'); ?>">
                                        <?php echo $is_active ? 'Active' : ($is_expired ? 'Expired' : ucfirst($key['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="revoke-key.php?id=<?php echo $key['id']; ?>" class="btn-small btn-danger" onclick="return confirm('Revoke this key?')">Revoke</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>