<?php
/**
 * Gallery Diagnostic Script
 * Access: https://primelandhotel.co.tz/diagnose-gallery.php
 * 
 * This script helps diagnose why gallery images are not showing
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Gallery Diagnostic - PrimeLand Hotel</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #e77a3a; border-bottom: 3px solid #e77a3a; padding-bottom: 10px; }
        h2 { color: #333; margin-top: 30px; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .warning { color: #ffc107; font-weight: bold; }
        .info { color: #17a2b8; }
        ul { list-style: none; padding-left: 0; }
        li { padding: 8px; margin: 5px 0; background: #f8f9fa; border-left: 4px solid #e77a3a; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        .section { margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Gallery Diagnostic Report</h1>
        <p><strong>Generated:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>

        <?php
        // Test 1: Check public path
        echo '<div class="section">';
        echo '<h2>1. Path Information</h2>';
        $publicPath = __DIR__;
        $galleryPath = $publicPath . '/gallery_photos';
        
        echo '<p><strong>Public Directory:</strong> <code>' . htmlspecialchars($publicPath) . '</code></p>';
        echo '<p><strong>Gallery Path:</strong> <code>' . htmlspecialchars($galleryPath) . '</code></p>';
        echo '</div>';

        // Test 2: Check if folder exists
        echo '<div class="section">';
        echo '<h2>2. Folder Existence Check</h2>';
        if (is_dir($galleryPath)) {
            echo '<p class="success">✅ Folder exists!</p>';
            echo '<p><strong>Readable:</strong> ' . (is_readable($galleryPath) ? '<span class="success">YES</span>' : '<span class="error">NO</span>') . '</p>';
            echo '<p><strong>Writable:</strong> ' . (is_writable($galleryPath) ? '<span class="success">YES</span>' : '<span class="warning">NO</span>') . '</p>';
        } else {
            echo '<p class="error">❌ Folder does NOT exist!</p>';
            echo '<p class="warning">⚠️ You need to create the folder: <code>public/gallery_photos/</code></p>';
        }
        echo '</div>';

        // Test 3: List images if folder exists
        if (is_dir($galleryPath)) {
            echo '<div class="section">';
            echo '<h2>3. Images Found</h2>';
            
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $images = [];
            
            try {
                $iterator = new DirectoryIterator($galleryPath);
                foreach ($iterator as $file) {
                    if ($file->isFile() && !$file->isDot()) {
                        $extension = strtolower($file->getExtension());
                        if (in_array($extension, $allowedExtensions)) {
                            $images[] = $file->getFilename();
                        }
                    }
                }
                
                // Also try glob as fallback
                if (empty($images)) {
                    foreach ($allowedExtensions as $ext) {
                        $files = glob($galleryPath . '/*.' . $ext);
                        $files = array_merge($files, glob($galleryPath . '/*.' . strtoupper($ext)));
                        if ($files) {
                            foreach ($files as $file) {
                                $images[] = basename($file);
                            }
                        }
                    }
                }
                
                $images = array_unique($images);
                sort($images);
                
                echo '<p><strong>Total Images Found:</strong> <span class="' . (count($images) > 0 ? 'success' : 'error') . '">' . count($images) . '</span></p>';
                
                if (count($images) > 0) {
                    echo '<ul>';
                    foreach ($images as $img) {
                        $imageUrl = '/gallery_photos/' . $img;
                        echo '<li>';
                        echo '<strong>' . htmlspecialchars($img) . '</strong><br>';
                        echo '<small class="info">URL: <code>' . htmlspecialchars($imageUrl) . '</code></small><br>';
                        echo '<img src="' . htmlspecialchars($imageUrl) . '" style="max-width: 200px; margin-top: 5px; border: 1px solid #ddd;" onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'block\';">';
                        echo '<span style="display:none; color: red;">⚠️ Image not accessible</span>';
                        echo '</li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<p class="error">❌ No images found in folder!</p>';
                    echo '<p class="warning">⚠️ Upload images to: <code>public/gallery_photos/</code></p>';
                }
            } catch (Exception $e) {
                echo '<p class="error">❌ Error reading folder: ' . htmlspecialchars($e->getMessage()) . '</p>';
            }
            echo '</div>';
        }

        // Test 4: Laravel path check (if Laravel is available)
        echo '<div class="section">';
        echo '<h2>4. Laravel Path Check</h2>';
        if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
            require_once __DIR__ . '/../vendor/autoload.php';
            $app = require_once __DIR__ . '/../bootstrap/app.php';
            $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
            $request = Illuminate\Http\Request::capture();
            
            $laravelPublicPath = public_path();
            $laravelGalleryPath = public_path('gallery_photos');
            
            echo '<p><strong>Laravel Public Path:</strong> <code>' . htmlspecialchars($laravelPublicPath) . '</code></p>';
            echo '<p><strong>Laravel Gallery Path:</strong> <code>' . htmlspecialchars($laravelGalleryPath) . '</code></p>';
            echo '<p><strong>Path Exists:</strong> ' . (is_dir($laravelGalleryPath) ? '<span class="success">YES</span>' : '<span class="error">NO</span>') . '</p>';
        } else {
            echo '<p class="warning">⚠️ Laravel not detected (this is OK if running standalone)</p>';
        }
        echo '</div>';

        // Test 5: Recommendations
        echo '<div class="section">';
        echo '<h2>5. Recommendations</h2>';
        if (!is_dir($galleryPath)) {
            echo '<p class="error">❌ <strong>Action Required:</strong> Create the folder <code>public/gallery_photos/</code></p>';
        } elseif (empty($images)) {
            echo '<p class="warning">⚠️ <strong>Action Required:</strong> Upload images to <code>public/gallery_photos/</code></p>';
        } else {
            echo '<p class="success">✅ Everything looks good! Images should display on the gallery page.</p>';
        }
        echo '</div>';
        ?>

        <div class="section">
            <h2>6. Next Steps</h2>
            <ol>
                <li>If folder doesn't exist: Create <code>public/gallery_photos/</code> folder</li>
                <li>Upload images (.jpg, .png, .gif, .webp) to the folder</li>
                <li>Set folder permissions to 755</li>
                <li>Set file permissions to 644</li>
                <li>Visit <a href="/gallery">Gallery Page</a> to verify</li>
                <li>Check browser console (F12) for any errors</li>
            </ol>
        </div>

        <hr>
        <p style="text-align: center; color: #666; margin-top: 30px;">
            <small>Delete this file after fixing the issue for security.</small>
        </p>
    </div>
</body>
</html>


