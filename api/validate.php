<?php
// API endpoint for external validation of license keys
header('Content-Type: application/json');
require_once '../config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

$license_key = strtoupper(trim($_GET['key'] ?? $_POST['key'] ?? ''));
$mod_id = (int)($_GET['mod_id'] ?? $_POST['mod_id'] ?? 0);

if(empty($license_key)) {
    echo json_encode(['success' => false, 'message' => 'License key is required']);
    exit();
}

$stmt = $conn->prepare('SELECT k.*, m.name as mod_name FROM license_keys k LEFT JOIN mods m ON k.mod_id = m.id WHERE k.license_key = ?');
$stmt->bind_param('s', $license_key);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid license key']);
    exit();
}

$key_data = $result->fetch_assoc();

// Check if key is active
if($key_data['status'] !== 'active') {
    echo json_encode(['success' => false, 'message' => 'License key has been revoked']);
    exit();
}

// Check if expired
$now = new DateTime();
$expires = new DateTime($key_data['expiration_date']);
if($now > $expires) {
    echo json_encode(['success' => false, 'message' => 'License key has expired', 'expired' => true]);
    exit();
}

// Check if key is for specific mod
if($mod_id > 0 && $key_data['mod_id'] !== null && $key_data['mod_id'] !== $mod_id) {
    echo json_encode(['success' => false, 'message' => 'License key is not valid for this mod']);
    exit();
}

// Valid key
$days_remaining = max(0, $expires->diff($now)->days);

echo json_encode([
    'success' => true,
    'message' => 'Valid license key',
    'data' => [
        'mod' => $key_data['mod_name'] ?? 'All Mods',
        'expires_at' => $key_data['expiration_date'],
        'days_remaining' => $days_remaining,
        'status' => 'active'
    ]
]);

$stmt->close();
$conn->close();
?>