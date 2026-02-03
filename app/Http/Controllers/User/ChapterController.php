<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Comic;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewChapterNotification;

class ChapterController extends Controller
{
    /**
     * Hiển thị form upload chapter
     */
    public function create(Comic $comic)
    {
        // Kiểm tra quyền: chỉ người tạo truyện mới được upload chapter
        $this->authorizeComic($comic);

        // Danh sách truyện của người đăng (để đổ vào dropdown)
        $myComics = Comic::where('created_by', Auth::id())
            ->orderByDesc('id')
            ->get(['id', 'title', 'slug', 'author', 'cover_image']);
        // cover_image nếu cover_url của bạn sinh từ cover_image

        // Lấy số chapter tiếp theo
        $nextChapterNumber = $comic->chapters()->max('chapter_number') + 1;

        return view('user.comics.chapters.create', compact('comic', 'myComics', 'nextChapterNumber'));
    }

    /**
     * Xử lý upload và lưu chapter
     */
    public function store(Request $request, Comic $comic)
    {
        // Kiểm tra quyền
        $this->authorizeComic($comic);

        // Validate
        $data = $request->validate([
            'chapter_number' => ['required', 'integer', 'min:1'],
            'title' => ['nullable', 'string', 'max:255'],
            'zip_file' => ['required', 'file', 'mimes:zip', 'max:102400'], // Tối đa 100MB
        ], [
            'chapter_number.required' => 'Vui lòng nhập số chapter.',
            'chapter_number.integer' => 'Số chapter phải là số nguyên.',
            'chapter_number.min' => 'Số chapter phải lớn hơn 0.',
            'zip_file.required' => 'Vui lòng chọn file ZIP chứa ảnh.',
            'zip_file.mimes' => 'File phải có định dạng ZIP.',
            'zip_file.max' => 'File ZIP không được vượt quá 100MB.',
        ]);

        // Kiểm tra chapter_number đã tồn tại chưa
        $existingChapter = Chapter::where('comic_id', $comic->id)
            ->where('chapter_number', $data['chapter_number'])
            ->first();

        if ($existingChapter) {
            return back()
                ->withInput()
                ->withErrors(['chapter_number' => 'Chapter số ' . $data['chapter_number'] . ' đã tồn tại cho truyện này.']);
        }

        // Xử lý upload và extract ZIP
        try {
            $zipFile = $request->file('zip_file');

            // Tạo thư mục tạm để extract ZIP
            // $tempExtractPath = storage_path('app/temp/chapters/' . uniqid());
            // File::makeDirectory($tempExtractPath, 0755, true);
            $tempExtractPath = storage_path('app/temp/chapters/' . uniqid());

            if (!File::exists($tempExtractPath)) {
                File::makeDirectory($tempExtractPath, 0755, true);
            }

            //
            // Extract ZIP file
            $zip = new ZipArchive;
            if ($zip->open($zipFile->getRealPath()) === TRUE) {
                $zip->extractTo($tempExtractPath);
                $zip->close();
            } else {
                throw new \Exception('Không thể giải nén file ZIP.');
            }

            // Lấy danh sách file ảnh từ thư mục đã extract
            $imageFiles = $this->getImageFiles($tempExtractPath);

            if (empty($imageFiles)) {
                File::deleteDirectory($tempExtractPath);
                return back()
                    ->withInput()
                    ->withErrors(['zip_file' => 'File ZIP không chứa ảnh hợp lệ (jpg, jpeg, png, gif, webp).']);
            }

            // Sắp xếp file ảnh theo số thứ tự
            natsort($imageFiles);

            $imageFiles = array_values($imageFiles);

            // Tạo thư mục lưu trữ chính thức cho chapter
            $chapterStoragePath = 'uploads/chapters/' . $comic->id . '/' . $data['chapter_number'];
            $fullStoragePath = storage_path('app/public/' . $chapterStoragePath);
            // File::makeDirectory($fullStoragePath, 0755, true);

            if (!File::exists($fullStoragePath)) {
                File::makeDirectory($fullStoragePath, 0755, true);
            }
            //

            // Di chuyển, resize và đổi tên file ảnh theo thứ tự 1, 2, 3, ...
            $pageCount = 0;
            $pageRows = [];

            foreach ($imageFiles as $index => $imageFile) {
                $sourcePath = $tempExtractPath . '/' . $imageFile;

                if (!File::exists($sourcePath)) continue;

                $extension = strtolower(pathinfo($imageFile, PATHINFO_EXTENSION));
                $pageIndex = $index + 1;

                // Chuyển đổi sang jpg để giảm dung lượng (trừ khi là webp)
                $finalExtension = ($extension === 'webp') ? 'webp' : 'jpg';
                $newFileName = $pageIndex . '.' . $finalExtension;
                $destinationPath = $fullStoragePath . '/' . $newFileName;

                // Resize và tối ưu ảnh trước khi lưu
                $this->resizeAndOptimizeImage($sourcePath, $destinationPath, $extension);

                $pageCount++;

                $pageRows[] = [
                    'page_index' => $pageIndex,
                    'image_path' => $chapterStoragePath . '/' . $newFileName, // uploads/chapters/{comic}/{chapter}/{page}.jpg
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Xóa thư mục tạm
            File::deleteDirectory($tempExtractPath);

            // Tạo record chapter trong database
            $chapter = new Chapter();
            $chapter->comic_id = $comic->id;
            $chapter->created_by = auth()->id();
            $chapter->chapter_number = $data['chapter_number'];
            $chapter->title = $data['title'] ?? 'Chapter ' . $data['chapter_number'];
            $chapter->images_path = $chapterStoragePath;
            $chapter->page_count = $pageCount;
            $chapter->views = 0;
            $chapter->save();

            // Insert pages
            $chapter->pages()->insert(
                array_map(fn($row) => $row + ['chapter_id' => $chapter->id], $pageRows)
            );

            // Cập nhật chapter_count và last_chapter_at của comic
            $comic->chapter_count = $comic->chapters()->count();
            $comic->last_chapter_at = now();
            $comic->save();

            // GỬI THÔNG BÁO CHO NGƯỜI THEO DÕI TRUYỆN
            $followers = $comic->followers;
            if ($followers->count() > 0) {
                Notification::send($followers, new NewChapterNotification($chapter, $comic));
            }

            $redirectTo = $request->input('redirect_to');

            return redirect()
                ->to($redirectTo ?: route('user.comics.show', $comic))
                ->with('success', 'Chapter đã được upload thành công!');
        } catch (\Exception $e) {
            // Xóa thư mục tạm nếu có lỗi
            if (isset($tempExtractPath) && File::exists($tempExtractPath)) {
                File::deleteDirectory($tempExtractPath);
            }

            return back()
                ->withInput()
                ->withErrors(['zip_file' => 'Có lỗi xảy ra khi xử lý file: ' . $e->getMessage()]);
        }
    }

    public function edit(Comic $comic, Chapter $chapter)
    {
        $this->authorizeComic($comic);

        $myComics = Comic::where('created_by', Auth::id())
            ->orderByDesc('id')
            ->get(['id', 'title', 'slug', 'author', 'cover_image']);

        $nextChapterNumber = $comic->chapters()->max('chapter_number') + 1;

        if ($chapter->comic_id !== $comic->id) {
            abort(404);
        }

        return view('user.comics.chapters.edit', compact('comic', 'myComics', 'nextChapterNumber', 'chapter'));
    }

    public function update(Request $request, Comic $comic, Chapter $chapter)
    {
        $this->authorizeComic($comic);

        if ($chapter->comic_id !== $comic->id) {
            abort(404);
        }

        $data = $request->validate([
            'chapter_number' => ['required', 'integer', 'min:1'],
            'title'          => ['nullable', 'string', 'max:255'],
            'zip_file'       => ['nullable', 'file', 'mimes:zip', 'max:102400'],
        ]);

        // Nếu đổi số chapter → check trùng
        if ($data['chapter_number'] != $chapter->chapter_number) {
            $exists = Chapter::where('comic_id', $comic->id)
                ->where('chapter_number', $data['chapter_number'])
                ->exists();

            if ($exists) {
                return back()->withErrors([
                    'chapter_number' => 'Chapter số này đã tồn tại.'
                ]);
            }
        }

        // Update thông tin cơ bản
        $chapter->chapter_number = $data['chapter_number'];
        $chapter->title = $data['title'] ?: 'Chapter ' . $data['chapter_number'];

        /**
         * ========== NẾU CÓ ZIP MỚI ==========
         */
        if ($request->hasFile('zip_file')) {
            try {
                // XÓA ảnh + pages cũ
                File::deleteDirectory(
                    storage_path('app/public/' . $chapter->images_path)
                );
                $chapter->pages()->delete();

                // ==== XỬ LÝ ZIP Y HỆT STORE() ====
                $zipFile = $request->file('zip_file');
                $tempPath = storage_path('app/temp/chapters/' . uniqid());

                File::makeDirectory($tempPath, 0755, true);

                $zip = new \ZipArchive;
                if ($zip->open($zipFile->getRealPath()) !== true) {
                    throw new \Exception('Không thể giải nén ZIP.');
                }

                $zip->extractTo($tempPath);
                $zip->close();

                $imageFiles = $this->getImageFiles($tempPath);
                natsort($imageFiles);
                $imageFiles = array_values($imageFiles);

                $chapterPath = 'uploads/chapters/' . $comic->id . '/' . $data['chapter_number'];
                $fullPath = storage_path('app/public/' . $chapterPath);

                File::makeDirectory($fullPath, 0755, true);

                $pages = [];
                foreach ($imageFiles as $i => $file) {
                    $source = $tempPath . '/' . $file;
                    $ext = pathinfo($file, PATHINFO_EXTENSION);
                    $pageIndex = $i + 1;
                    $fileName = $pageIndex . '.jpg';

                    $this->resizeAndOptimizeImage(
                        $source,
                        $fullPath . '/' . $fileName,
                        $ext
                    );

                    $pages[] = [
                        'chapter_id' => $chapter->id,
                        'page_index' => $pageIndex,
                        'image_path' => $chapterPath . '/' . $fileName,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                File::deleteDirectory($tempPath);

                $chapter->images_path = $chapterPath;
                $chapter->page_count = count($pages);

                $chapter->pages()->insert($pages);
            } catch (\Exception $e) {
                return back()->withErrors([
                    'zip_file' => 'Lỗi xử lý ZIP: ' . $e->getMessage()
                ]);
            }
        }

        $redirectTo = $request->input('redirect_to');

        $chapter->save();

        return redirect()
            ->to($redirectTo ?: route('user.comics.show', $comic))
            ->with('success', 'Chapter đã được cập nhật thành công!');
    }

    /**
     * Xóa chapter
     */
    public function destroy(Request $request, Comic $comic, Chapter $chapter)
    {
        $this->authorizeComic($comic);

        if ($chapter->comic_id !== $comic->id) {
            abort(404);
        }

        if ($chapter->images_path) {
            File::deleteDirectory(storage_path('app/public/' . $chapter->images_path));
        }

        $chapter->delete();

        $comic->chapter_count = $comic->chapters()->count();
        $comic->last_chapter_at = $comic->chapters()->latest('created_at')->value('created_at');
        $comic->save();

        $redirectTo = $request->input('redirect_to');

        return redirect()
            ->to($redirectTo ?: route('user.comics.show', $comic))
            ->with('success', 'Chapter đã được xóa thành công!');
    }

    /**
     * Lấy danh sách file ảnh từ thư mục (trả về đường dẫn tương đối)
     */
    private function getImageFiles($directory, $basePath = null)
    {
        $imageFiles = [];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!is_dir($directory)) {
            return $imageFiles;
        }

        // Lưu base path lần đầu tiên
        if ($basePath === null) {
            $basePath = $directory;
        }

        $items = scandir($directory);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $itemPath = $directory . '/' . $item;

            if (is_file($itemPath)) {
                $extension = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                if (in_array($extension, $allowedExtensions)) {
                    // Trả về đường dẫn tương đối từ basePath
                    $relativePath = str_replace($basePath . '/', '', $itemPath);
                    $imageFiles[] = $relativePath;
                }
            } elseif (is_dir($itemPath)) {
                // Đệ quy vào thư mục con
                $subFiles = $this->getImageFiles($itemPath, $basePath);
                $imageFiles = array_merge($imageFiles, $subFiles);
            }
        }

        return $imageFiles;
    }

    /**
     * Resize và tối ưu ảnh: giảm xuống 1080p nếu lớn hơn, giữ nguyên nếu nhỏ hơn
     * 
     * @param string $sourcePath Đường dẫn file ảnh gốc
     * @param string $destinationPath Đường dẫn file ảnh đích
     * @param string $extension Định dạng file gốc
     */
    private function resizeAndOptimizeImage($sourcePath, $destinationPath, $extension)
    {
        // Kiểm tra GD extension
        if (!extension_loaded('gd')) {
            // Nếu không có GD, chỉ copy file
            File::copy($sourcePath, $destinationPath);
            return;
        }

        // Đọc ảnh gốc
        $image = null;
        switch (strtolower($extension)) {
            case 'jpg':
            case 'jpeg':
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case 'png':
                $image = imagecreatefrompng($sourcePath);
                break;
            case 'gif':
                $image = imagecreatefromgif($sourcePath);
                break;
            case 'webp':
                if (function_exists('imagecreatefromwebp')) {
                    $image = imagecreatefromwebp($sourcePath);
                } else {
                    File::copy($sourcePath, $destinationPath);
                    return;
                }
                break;
            default:
                File::copy($sourcePath, $destinationPath);
                return;
        }

        if (!$image) {
            File::copy($sourcePath, $destinationPath);
            return;
        }

        // Lấy kích thước gốc
        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);

        // 1080p = 1920x1080 (Full HD)
        $maxWidth = 1920;
        $maxHeight = 1080;

        // Tính toán kích thước mới (giữ tỷ lệ)
        $newWidth = $originalWidth;
        $newHeight = $originalHeight;

        if ($originalWidth > $maxWidth || $originalHeight > $maxHeight) {
            $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
            $newWidth = (int)($originalWidth * $ratio);
            $newHeight = (int)($originalHeight * $ratio);
        }

        // Xác định extension của file đích
        $destExtension = strtolower(pathinfo($destinationPath, PATHINFO_EXTENSION));

        // Nếu không cần resize, chỉ tối ưu chất lượng
        if ($newWidth === $originalWidth && $newHeight === $originalHeight) {
            // Giữ nguyên kích thước nhưng tối ưu chất lượng
            $this->saveOptimizedImage($image, $destinationPath, $destExtension);
            imagedestroy($image);
            return;
        }

        // Tạo ảnh mới với kích thước đã resize
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Giữ độ trong suốt cho PNG/GIF (nếu file đích là PNG)
        if ($destExtension === 'png') {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        // Resize ảnh với chất lượng tốt
        imagecopyresampled(
            $newImage,
            $image,
            0,
            0,
            0,
            0,
            $newWidth,
            $newHeight,
            $originalWidth,
            $originalHeight
        );

        // Lưu ảnh đã tối ưu
        $this->saveOptimizedImage($newImage, $destinationPath, $destExtension);

        // Giải phóng bộ nhớ
        imagedestroy($image);
        imagedestroy($newImage);
    }

    /**
     * Lưu ảnh với chất lượng tối ưu
     */
    private function saveOptimizedImage($image, $destinationPath, $originalExtension)
    {
        $extension = strtolower(pathinfo($destinationPath, PATHINFO_EXTENSION));

        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                // JPEG quality: 85 (cân bằng giữa chất lượng và dung lượng)
                imagejpeg($image, $destinationPath, 85);
                break;
            case 'png':
                // PNG compression: 6 (0-9, 6 là cân bằng tốt)
                imagepng($image, $destinationPath, 6);
                break;
            case 'webp':
                if (function_exists('imagewebp')) {
                    // WebP quality: 85
                    imagewebp($image, $destinationPath, 85);
                } else {
                    // Fallback: chuyển sang JPEG
                    imagejpeg($image, $destinationPath, 85);
                }
                break;
            default:
                imagejpeg($image, $destinationPath, 85);
        }
    }

    /**
     * Kiểm tra quyền: chỉ người tạo truyện mới được upload chapter
     */
    protected function authorizeComic(Comic $comic)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Bạn cần đăng nhập để thực hiện thao tác này.');
        }

        if ((int)$comic->created_by !== (int)$user->id) {
            abort(403, 'Bạn chỉ được upload chapter cho truyện do chính bạn đăng.');
        }
    }
}
