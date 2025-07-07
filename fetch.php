<?php
header('Content-Type: application/json');
if (!file_exists('photos.json')) file_put_contents('photos.json', json_encode([]));
$photos = json_decode(file_get_contents('photos.json'), true);
if (!is_array($photos)) $photos = [];
echo json_encode($photos, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
?>