<?php
if (!isset($_GET['room']) || !isset($_GET['user'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Room or user not specified."]);
    exit;
}

// Sanitize input
$room = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['room']);
$user = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['user']);
$baseDir = __DIR__ . "/$room/$user";

if (!is_dir($baseDir)) {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "User directory not found."]);
    exit;
}

// Get all PNG files in the folder
$images = [];
foreach (glob("$baseDir/*.png") as $file) {
    $images[] = [
        'title' => basename($file),
        'path' => "$room/$user/" . basename($file),
        'timestamp' => filemtime($file),
    ];
}

// Sort images by timestamp ascending
usort($images, function ($a, $b) {
    return $a['timestamp'] - $b['timestamp'];
});

header('Content-Type: application/json');
echo json_encode($images);
?>

