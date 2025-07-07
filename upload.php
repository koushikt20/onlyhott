<?php
date_default_timezone_set('Asia/Kolkata');
header('Content-Type: application/json');
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}
if (!file_exists('uploads')) mkdir('uploads', 0777, true);
if (!file_exists('photos.json')) file_put_contents('photos.json', json_encode([]));
if (!isset($_FILES['media']) || $_FILES['media']['error'] !== UPLOAD_ERR_OK) {
    $msg = 'No file uploaded or upload error';
    if (isset($_FILES['media']['error'])) {
        switch ($_FILES['media']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $msg = 'File is too large (over server limit)';
                break;
            case UPLOAD_ERR_PARTIAL:
                $msg = 'File only partially uploaded';
                break;
            case UPLOAD_ERR_NO_FILE:
                $msg = 'No file uploaded';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $msg = 'Missing a temporary folder';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $msg = 'Failed to write file to disk';
                break;
            case UPLOAD_ERR_EXTENSION:
                $msg = 'Upload stopped by extension';
                break;
        }
    }
    echo json_encode(['success' => false, 'message' => $msg]);
    exit;
}
$file = $_FILES['media'];
if ($file['size'] > 1024 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'File is too large. Max 1GB supported.']);
    exit;
}
$allowedTypes = [
    'image/jpeg', 'image/png', 'image/gif',
    'video/mp4', 'video/webm', 'video/ogg'
];
if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Only JPG, PNG, GIF, MP4, WEBM, OGG are allowed']);
    exit;
}
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid() . '.' . $extension;
$destination = 'uploads/' . $filename;
if (!move_uploaded_file($file['tmp_name'], $destination)) {
    echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
    exit;
}
$note = isset($_POST['note']) ? trim($_POST['note']) : "";
$ads = [
    '<!--ad-split--><div class="ad-mobile"><script type="text/javascript">atOptions = {\'key\' : \'560534b4dc0f7f6c7ef15c55a7b64fb5\',\'format\' : \'iframe\',\'height\' : 90,\'width\' : 320,\'params\' : {}};</script><script type="text/javascript" src="//www.highperformanceformat.com/560534b4dc0f7f6c7ef15c55a7b64fb5/invoke.js"></script></div>',
    '<!--ad-split--><div class="ad-mobile"><script type="text/javascript" src="//pl27080788.profitableratecpm.com/ce/a3/a4/cea3a4eb9d7506baa41a02c72a8b058f.js"></script></div>',
    '<!--ad-split--><div class="ad-mobile"><script type="text/javascript" src="//pl27080838.profitableratecpm.com/1c/4b/6e/1c4b6e2d6269ce45aed419c633aa86f6.js"></script></div>',
    '<!--ad-split--><div class="ad-mobile"><script async="async" data-cfasync="false" src="//pl27080831.profitableratecpm.com/a6790d6a41c024632dc3ffcbddfc7ace/invoke.js"></script><div id="container-a6790d6a41c024632dc3ffcbddfc7ace"></div></div>'
];
$adHtml = $ads[array_rand($ads)];
$noteWithAds = $note . $adHtml;

$photos = json_decode(file_get_contents('photos.json'), true);
$newPhoto = [
    'filename' => $filename,
    'timestamp' => date('Y-m-d H:i:s'),
    'type' => strpos($file['type'], 'video') === 0 ? 'video' : 'image',
    'note' => $noteWithAds
];
array_unshift($photos, $newPhoto);
file_put_contents('photos.json', json_encode($photos, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
echo json_encode(['success' => true, 'filename' => $filename]);
?>