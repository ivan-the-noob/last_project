<?php
session_start();
require '../../../../db.php';


// Get and validate input
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$action = $_POST['action'] ?? '';

if (!$email || !in_array($action, ['ban', 'unban'])) {
    http_response_code(400);
    die('Invalid request');
}

// Update database
try {
    $banStatus = ($action === 'ban') ? 1 : 0;
    $stmt = $conn->prepare("UPDATE users SET is_ban = ? WHERE email = ?");
    $stmt->bind_param("is", $banStatus, $email);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        echo 'success';
    } else {
        http_response_code(404);
        echo 'User not found';
    }
} catch (Exception $e) {
    http_response_code(500);
    echo 'Database error';
}
?>