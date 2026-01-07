# Hướng dẫn sửa php.ini cho XAMPP để upload file lớn

## Vấn đề
Lỗi: `POST Content-Length exceeds the limit of 41943040 bytes` (40MB)

## Giải pháp

### Bước 1: Tìm file php.ini của XAMPP
1. Mở XAMPP Control Panel
2. Click nút "Config" bên cạnh Apache
3. Chọn "PHP (php.ini)" - file sẽ mở trong Notepad

**Hoặc tìm thủ công:**
- Đường dẫn thường là: `C:\xampp\php\php.ini`

### Bước 2: Sửa các giá trị sau trong php.ini

Tìm và sửa các dòng sau (bỏ dấu `;` nếu có ở đầu dòng):

```ini
upload_max_filesize = 150M
post_max_size = 150M
max_execution_time = 300
max_input_time = 300
memory_limit = 256M
```

**Lưu ý:** 
- Tìm các dòng này (có thể có `;` ở đầu để comment)
- Sửa giá trị thành `150M` (hoặc lớn hơn nếu cần)
- Đảm bảo không có dấu `;` ở đầu dòng

### Bước 3: Lưu file và khởi động lại Apache

1. Lưu file php.ini
2. Trong XAMPP Control Panel, click "Stop" cho Apache
3. Click "Start" lại cho Apache

### Bước 4: Kiểm tra

Tạo file `test_upload.php` trong thư mục `htdocs`:

```php
<?php
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "max_execution_time: " . ini_get('max_execution_time') . "<br>";
echo "memory_limit: " . ini_get('memory_limit') . "<br>";
```

Truy cập: `http://localhost/test_upload.php`

Nếu thấy các giá trị đã được cập nhật thành `150M`, bạn đã thành công!

## Giải pháp thay thế (nếu không thể sửa php.ini)

Nếu bạn không có quyền sửa php.ini, có thể tạo file `.user.ini` trong thư mục `public` (đã được tạo sẵn), nhưng cần đảm bảo PHP được cấu hình để đọc file này.

