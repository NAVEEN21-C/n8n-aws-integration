<?php
// Gallery endpoint - returns list of uploaded files
header('Content-Type: application/json');

$uploadDir = __DIR__ . '/uploads';
$files = [];

if (is_dir($uploadDir)) {
    $items = scandir($uploadDir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        
        $path = $uploadDir . '/' . $item;
        if (!is_file($path)) continue;
        
        $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
        $type = in_array($ext, ['jpg','jpeg','png','gif']) ? 'image' : 'video';
        
        $files[] = [
            'name' => $item,
            'url' => "uploads/$item",
            'type' => $type
        ];
    }
}

// Sort by newest first
usort($files, function($a, $b) {
    return filemtime($uploadDir . '/' . $b['name']) - filemtime($uploadDir . '/' . $a['name']);
});

echo json_encode($files);
