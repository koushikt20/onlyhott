<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}
if (!file_exists('photos.json')) file_put_contents('photos.json', json_encode([]));
$photos = json_decode(file_get_contents('photos.json'), true);
if (!is_array($photos)) $photos = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $op = $_POST['op'];
    $filename = $_POST['filename'] ?? '';
    $uploadthing_url = $_POST['uploadthing_url'] ?? '';
    if ($op === 'delete') {
        $found = false;
        foreach ($photos as $i => $p) {
            if (
                (!$uploadthing_url && isset($p['filename']) && $p['filename'] === $filename) ||
                ($uploadthing_url && isset($p['is_uploadthing']) && $p['is_uploadthing'] && $p['url'] === $uploadthing_url)
            ) {
                $found = true;
                // Remove local file if not uploadthing
                if (!$p['is_uploadthing'] && isset($p['filename']) && file_exists('uploads/' . $p['filename'])) {
                    unlink('uploads/' . $p['filename']);
                }
                unset($photos[$i]);
                break;
            }
        }
        $photos = array_values($photos);
        file_put_contents('photos.json', json_encode($photos));
        echo json_encode(['success' => $found]);
        exit;
    } elseif ($op === 'edit') {
        $note = $_POST['note'];
        $success = false;
        $ads = [
            '<!--ad-split--><div class="ad-mobile"><script type="text/javascript">atOptions = {\'key\' : \'560534b4dc0f7f6c7ef15c55a7b64fb5\',\'format\' : \'iframe\',\'height\' : 90,\'width\' : 320,\'params\' : {}};</script><script type="text/javascript" src="//www.highperformanceformat.com/560534b4dc0f7f6c7ef15c55a7b64fb5/invoke.js"></script></div>',
            '<!--ad-split--><div class="ad-mobile"><script type="text/javascript" src="//pl27080788.profitableratecpm.com/ce/a3/a4/cea3a4eb9d7506baa41a02c72a8b058f.js"></script></div>',
            '<!--ad-split--><div class="ad-mobile"><script type="text/javascript" src="//pl27080838.profitableratecpm.com/1c/4b/6e/1c4b6e2d6269ce45aed419c633aa86f6.js"></script></div>',
            '<!--ad-split--><div class="ad-mobile"><script async="async" data-cfasync="false" src="//pl27080831.profitableratecpm.com/a6790d6a41c024632dc3ffcbddfc7ace/invoke.js"></script><div id="container-a6790d6a41c024632dc3ffcbddfc7ace"></div></div>'
        ];
        $adHtml = $ads[array_rand($ads)];
        foreach ($photos as &$p) {
            if (
                (!$uploadthing_url && isset($p['filename']) && $p['filename'] === $filename) ||
                ($uploadthing_url && isset($p['is_uploadthing']) && $p['is_uploadthing'] && $p['url'] === $uploadthing_url)
            ) {
                $p['note'] = trim($note) . $adHtml;
                $success = true;
                break;
            }
        }
        file_put_contents('photos.json', json_encode($photos));
        echo json_encode(['success' => $success]);
        exit;
    }
}
echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>