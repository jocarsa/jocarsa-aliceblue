<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['username']) || !isset($data['room']) || !isset($data['frame'])) {
        http_response_code(400);
        echo "Invalid input.";
        exit;
    }

    // Sanitize inputs
    $username = preg_replace('/[^a-zA-Z0-9_-]/', '', $data['username']);
    $room = preg_replace('/[^a-zA-Z0-9_-]/', '', $data['room']);
    $frameData = $data['frame'];

    // Decode the Base64 image data
    if (preg_match('/^data:image\/(\w+);base64,/', $frameData, $type)) {
        $frameData = substr($frameData, strpos($frameData, ',') + 1);
        $frameData = base64_decode($frameData);

        if ($frameData === false) {
            http_response_code(400);
            echo "Failed to decode image.";
            exit;
        }
    } else {
        http_response_code(400);
        echo "Invalid image format.";
        exit;
    }

    // Define the directory path for the room and user
    $dir = __DIR__ . "/$room/$username";

    // Create the directory structure if it doesn't exist
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }

    // Save the frame with epoch timestamp as the filename
    $filePath = "$dir/" . time() . ".jpg";
    if (file_put_contents($filePath, $frameData)) {
        echo "Frame saved as $filePath.";
    } else {
        http_response_code(500);
        echo "Failed to save frame.";
    }
} else {
    http_response_code(405);
    echo "Method not allowed.";
}
?>

