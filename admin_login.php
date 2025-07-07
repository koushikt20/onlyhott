<?php
session_start();
header('Content-Type: application/json');
$passwordFile = 'admin_pw.json';
$defaultPw = 'admin123';

if (!file_exists($passwordFile)) {
    file_put_contents($passwordFile, json_encode(['password' => password_hash($defaultPw, PASSWORD_DEFAULT)]));
}
$data = json_decode(file_get_contents($passwordFile), true);

$input = json_decode(file_get_contents('php://input'), true);
$pw = $input['password'] ?? '';

if (password_verify($pw, $data['password'])) {
    $_SESSION['admin_logged_in'] = true;
    echo json_encode(['success' => true]);
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Incorrect password']);
    exit;
}
?>