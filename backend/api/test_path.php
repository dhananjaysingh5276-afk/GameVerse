<?php
echo "✅ API folder is accessible!<br>";
echo "Current file: " . __FILE__ . "<br>";
echo "Directory: " . __DIR__ . "<br>";

// Check if bootstrap exists
$bootstrap_path = __DIR__ . '/../bootstrap.php';
if (file_exists($bootstrap_path)) {
    echo "✅ bootstrap.php found at: $bootstrap_path<br>";
} else {
    echo "❌ bootstrap.php NOT found at: $bootstrap_path<br>";
}

// List files in current directory
echo "<br>Files in API folder:<br>";
$files = scandir(__DIR__);
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        echo "- $file<br>";
    }
}
?>