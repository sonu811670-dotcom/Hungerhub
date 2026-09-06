<?php
/**
 * 1-Click Server Unzipper for InfinityFree
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>HungerHub Asset Extraction Tool</h2>";

// 1. Extract images.zip
if (file_exists('images.zip')) {
    $zip = new ZipArchive;
    $res = $zip->open('images.zip');
    if ($res === TRUE) {
        $zip->extractTo(__DIR__);
        $zip->close();
        echo "<p style='color:green;'>✅ <strong>images.zip</strong> extracted successfully!</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed to extract images.zip (Error code: $res)</p>";
    }
} else {
    echo "<p style='color:orange;'>⚠️ images.zip not found in current directory.</p>";
}

// 2. Extract uploads.zip
if (file_exists('uploads.zip')) {
    $zip = new ZipArchive;
    $res = $zip->open('uploads.zip');
    if ($res === TRUE) {
        $zip->extractTo(__DIR__);
        $zip->close();
        echo "<p style='color:green;'>✅ <strong>uploads.zip</strong> extracted successfully!</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed to extract uploads.zip (Error code: $res)</p>";
    }
} else {
    echo "<p style='color:orange;'>⚠️ uploads.zip not found in current directory.</p>";
}

// 3. Check extracted folders
if (is_dir('images')) {
    $count = count(glob('images/*.*'));
    echo "<p>📁 <code>images/</code> folder contains $count files.</p>";
}
if (is_dir('uploads')) {
    $count = count(glob('uploads/*.*'));
    echo "<p>📁 <code>uploads/</code> folder contains $count files.</p>";
}

echo "<hr><p><a href='index.php' style='display:inline-block;padding:10px 20px;background:#28a745;color:white;text-decoration:none;border-radius:6px;'>🚀 Open Website Now</a></p>";