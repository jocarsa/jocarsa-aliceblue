<?php
function getLastImageInfo($folderPath)
{
    $files = glob("$folderPath/*.png");
    if (!$files) return null;

    // Get the most recent file by sorting
    usort($files, function ($a, $b) {
        return filemtime($b) - filemtime($a);
    });

    $latestFile = $files[0];
    $timestamp = filemtime($latestFile);

    return [
        'path' => $latestFile,
        'timestamp' => $timestamp,
    ];
}

function getUsersLastImages($roomDir)
{
    $users = array_filter(glob("$roomDir/*"), 'is_dir');
    $data = [];

    foreach ($users as $userDir) {
        $username = basename($userDir);
        $lastImage = getLastImageInfo($userDir);
        if ($lastImage) {
            $data[] = [
                'username' => $username,
                'image' => $lastImage['path'],
                'timestamp' => $lastImage['timestamp'],
            ];
        }
    }

    return $data;
}

// Check if the room parameter is provided
if (!isset($_GET['room']) || empty($_GET['room'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Room not specified."]);
    exit;
}

$room = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['room']); // Sanitize room name
$baseDir = $room."/";

if (!is_dir($baseDir)) {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Room not found."]);
    exit;
}

$imagesData = getUsersLastImages($baseDir);

header('Content-Type: application/json');
echo json_encode($imagesData);
?>

