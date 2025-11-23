<?php
// Simple secure PHP uploader for images and videos.
// Save this file as upload.php inside /var/www/html/secondsite

set_time_limit(300);
$maxFileSize = 20 * 1024 * 1024; // 20 MB
$allowedExt = ['jpg','jpeg','png','gif','mp4','webm','mov','avi'];
$uploadDir = __DIR__ . '/uploads';

// Create uploads directory if missing
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

function safe_filename($name) {
    return preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $name);
}

function trigger_n8n_webhook($filename, $size, $url) {
    $webhookUrl = 'http://localhost:5678/webhook/file-uploaded';
    $data = json_encode([
        'filename' => $filename,
        'size' => $size,
        'url' => $url
    ]);

    $ch = curl_init($webhookUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 2); // Don't wait too long
    curl_exec($ch);
    curl_close($ch);
}

$results = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['media_files'])) {
    $files = $_FILES['media_files'];
    $count = count($files['name']);

    for ($i = 0; $i < $count; $i++) {
        $origName = $files['name'][$i];
        $tmp = $files['tmp_name'][$i];
        $err = $files['error'][$i];
        $size = $files['size'][$i];

        if ($err !== UPLOAD_ERR_OK) {
            $results[] = ['file'=>$origName,'status'=>'error','msg'=>"Upload error code $err"];
            continue;
        }
        if ($size > $maxFileSize) {
            $results[] = ['file'=>$origName,'status'=>'error','msg'=>"File too large"];
            continue;
        }

        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt)) {
            $results[] = ['file'=>$origName,'status'=>'error','msg'=>"Extension not allowed"];
            continue;
        }

        $safe = safe_filename(pathinfo($origName, PATHINFO_FILENAME));
        $newName = $safe . '_' . time() . '.' . $ext;
        $target = $uploadDir . DIRECTORY_SEPARATOR . $newName;

        if (move_uploaded_file($tmp, $target)) {
            chmod($target, 0644);
            $publicUrl = "http://uplife.duckdns.org/secondsite/uploads/$newName";
            trigger_n8n_webhook($origName, $size, $publicUrl);
            $results[] = ['file'=>$origName,'status'=>'ok','msg'=>"Saved as uploads/$newName"];
        } else {
            $results[] = ['file'=>$origName,'status'=>'error','msg'=>"Failed to move uploaded file"];
        }
    }
} else {
    $results[] = ['status'=>'error','msg'=>'No files uploaded.'];
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Upload Results</title>
<style>
body{font-family:system-ui,Arial;margin:18px}
table{border-collapse:collapse;width:100%}
td,th{padding:8px;border:1px solid #ddd;text-align:left}
</style>
</head>
<body>
<h2>Upload Results</h2>
<table>
<tr><th>File</th><th>Status</th><th>Message</th></tr>
<?php foreach($results as $r): ?>
<tr>
<td><?= htmlspecialchars($r['file'] ?? '') ?></td>
<td><?= htmlspecialchars($r['status']) ?></td>
<td><?= htmlspecialchars($r['msg'] ?? '') ?></td>
</tr>
<?php endforeach; ?>
</table>
<p><a href="index.html">Back to Upload Page</a></p>
</body>
</html>
