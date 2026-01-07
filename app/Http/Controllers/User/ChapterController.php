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

            // Di chuyển và đổi tên file ảnh theo thứ tự 1, 2, 3, ...
            $pageCount = 0;
            $pageRows = [];

            foreach ($imageFiles as $index => $imageFile) {
                $sourcePath = $tempExtractPath . '/' . $imageFile;

                if (!File::exists($sourcePath)) continue;

                $extension = strtolower(pathinfo($imageFile, PATHINFO_EXTENSION));
                $pageIndex = $index + 1;

                $newFileName = $pageIndex . '.' . $extension;
                $destinationPath = $fullStoragePath . '/' . $newFileName;

                File::move($sourcePath, $destinationPath);
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

            return redirect()
                ->route('user.comics.show', $comic)
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

    /**
     * Lấy danh sách file ảnh từ thư mục
     */
    private function getImageFiles($directory)
    {
        $imageFiles = [];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!is_dir($directory)) {
            return $imageFiles;
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
                    $imageFiles[] = $item;
                }
            } elseif (is_dir($itemPath)) {
                // Đệ quy vào thư mục con
                $subFiles = $this->getImageFiles($itemPath);
                $imageFiles = array_merge($imageFiles, $subFiles);
            }
        }

        return $imageFiles;
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
