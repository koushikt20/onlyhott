<?php
session_start();
header('Content-Type: application/json');
$passwordFile = 'admin_pw.json';
$secretCode = 'NATURESECRET';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}
$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['secret']) && $data['secret'] === $secretCode) {
    $_SESSION['can_change_password'] = true;
    echo json_encode(['success' => true, 'can_change' => true]);
    exit;
}
if (!isset($_SESSION['can_change_password']) || $_SESSION['can_change_password'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Secret code not verified']);
    exit;
}
if (!isset($data['new_password']) || strlen($data['new_password']) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password too short']);
    exit;
}
file_put_contents($passwordFile, json_encode([
    'password' => password_hash($data['new_password'], PASSWORD_DEFAULT)
]));
unset($_SESSION['can_change_password']);
echo json_encode(['success' => true]);
?>