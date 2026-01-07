<?php
/**
 * File kiểm tra cấu hình upload của PHP
 * Truy cập: http://localhost/truyenvh/public/check_upload_config.php
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kiểm tra cấu hình Upload PHP</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
        .config-item { margin: 15px 0; padding: 15px; background: #f8f9fa; border-left: 4px solid #007bff; }
        .config-item strong { color: #007bff; display: inline-block; min-width: 200px; }
        .ok { color: #28a745; font-weight: bold; }
        .warning { color: #ffc107; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .info { margin-top: 20px; padding: 15px; background: #e7f3ff; border-left: 4px solid #007bff; }
        .info ul { margin: 10px 0; padding-left: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Kiểm tra cấu hình Upload PHP</h1>

        <?php
        $uploadMaxFilesize = ini_get('upload_max_filesize');
        $postMaxSize = ini_get('post_max_size');
        $maxExecutionTime = ini_get('max_execution_time');
        $maxInputTime = ini_get('max_input_time');
        $memoryLimit = ini_get('memory_limit');

        // Chuyển đổi sang bytes để so sánh
        function convertToBytes($value) {
            $value = trim($value);
            $last = strtolower($value[strlen($value)-1]);
            $value = (int) $value;
            switch($last) {
                case 'g': $value *= 1024;
                case 'm': $value *= 1024;
                case 'k': $value *= 1024;
            }
            return $value;
        }

        $uploadMaxBytes = convertToBytes($uploadMaxFilesize);
        $postMaxBytes = convertToBytes($postMaxSize);
        $requiredBytes = 150 * 1024 * 1024; // 150MB

        $uploadOk = $uploadMaxBytes >= $requiredBytes;
        $postOk = $postMaxBytes >= $requiredBytes;
        ?>

        <div class="config-item">
            <strong>upload_max_filesize:</strong>
            <span class="<?php echo $uploadOk ? 'ok' : 'error'; ?>">
                <?php echo $uploadMaxFilesize; ?>
                <?php echo $uploadOk ? '✅ OK' : '❌ Cần tối thiểu 150M'; ?>
            </span>
        </div>

        <div class="config-item">
            <strong>post_max_size:</strong>
            <span class="<?php echo $postOk ? 'ok' : 'error'; ?>">
                <?php echo $postMaxSize; ?>
                <?php echo $postOk ? '✅ OK' : '❌ Cần tối thiểu 150M'; ?>
            </span>
        </div>

        <div class="config-item">
            <strong>max_execution_time:</strong>
            <span><?php echo $maxExecutionTime; ?> giây</span>
        </div>

        <div class="config-item">
            <strong>max_input_time:</strong>
            <span><?php echo $maxInputTime; ?> giây</span>
        </div>

        <div class="config-item">
            <strong>memory_limit:</strong>
            <span><?php echo $memoryLimit; ?></span>
        </div>

        <?php if (!$uploadOk || !$postOk): ?>
            <div class="info">
                <h3>⚠️ Cần sửa cấu hình PHP</h3>
                <p>Để upload file lớn hơn 40MB, bạn cần sửa file <strong>php.ini</strong> của XAMPP:</p>
                <ul>
                    <li>Mở XAMPP Control Panel</li>
                    <li>Click "Config" bên cạnh Apache → Chọn "PHP (php.ini)"</li>
                    <li>Tìm và sửa các dòng sau:</li>
                </ul>
                <pre style="background: #f4f4f4; padding: 15px; border-radius: 4px; overflow-x: auto;">
upload_max_filesize = 150M
post_max_size = 150M
max_execution_time = 300
max_input_time = 300
memory_limit = 256M</pre>
                <p><strong>Lưu ý:</strong> Sau khi sửa, cần khởi động lại Apache trong XAMPP Control Panel.</p>
                <p>Xem file <code>HUONG_DAN_SUA_PHP_INI.md</code> trong thư mục gốc để biết chi tiết.</p>
            </div>
        <?php else: ?>
            <div class="info">
                <h3>✅ Cấu hình đã đúng!</h3>
                <p>Bạn có thể upload file lên đến 150MB. Nếu vẫn gặp lỗi, hãy kiểm tra:</p>
                <ul>
                    <li>Đã khởi động lại Apache sau khi sửa php.ini chưa?</li>
                    <li>File .htaccess có được Apache đọc không?</li>
                    <li>Kiểm tra log lỗi của Apache/PHP</li>
                </ul>
            </div>
        <?php endif; ?>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px;">
            <p>File này có thể xóa sau khi đã kiểm tra xong.</p>
        </div>
    </div>
</body>
</html>

