<?php

namespace App\Http\Controllers\Admin;

use App\Models\Comic;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class ComicController extends Controller
{
    /**
     * Ép các field rỗng ("") thành null
     */
    private function castEmptyToNull(Request $request, array $fields): void
    {
        foreach ($fields as $f) {
            $val = trim((string) $request->input($f));
            if ($val === '') {
                $request->merge([$f => null]);
            }
        }
    }

    /**
     * Rule validate cho Comic
     */
    private function getValidationRules(?Comic $comic = null): array
    {
        $comicId = $comic?->id ?? 'NULL';

        return [
            'title'         => 'required|string|max:255',
            'slug'          => 'nullable|string|max:255|unique:comics,slug,' . $comicId,
            'description'   => 'nullable|string',
            'author'        => 'nullable|string|max:255',
            'status'        => 'required|in:ongoing,completed,dropped',

            'cover_image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',

            'chapter_count' => 'nullable|integer|min:0',
            'published_at'  => 'nullable|date',

            // many-to-many categories
            'category_ids'   => 'required|array',
            'category_ids.*' => 'exists:categories,id',
        ];
    }

    /**
     * Upload / thay ảnh bìa
     */
    private function uploadCoverImage(Request $request, ?string $oldImage = null): ?string
    {
        if ($request->hasFile('cover_image')) {
            // Xóa ảnh cũ nếu có
            if ($oldImage) {
                $this->deleteCoverImage($oldImage);
            }

            $file = $request->file('cover_image');
            $imageName = uniqid('comic_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/comics'), $imageName);

            return $imageName;
        }

        // Không upload mới → giữ ảnh cũ
        return $oldImage;
    }

    /**
     * Xóa file ảnh bìa trên ổ đĩa
     */
    private function deleteCoverImage(?string $imageName): void
    {
        if ($imageName) {
            $path = public_path('uploads/comics/' . $imageName);
            if (File::exists($path)) {
                File::delete($path);
            }
        }
    }

    /**
     * Danh sách truyện
     */
    public function index()
    {
        $comics = Comic::with('categories')->latest()->get();
        return view('comics.index', compact('comics'));
    }

    /**
     * Form tạo truyện
     */
    public function create()
    {
        $categories = Category::all();
        return view('comics.create', compact('categories'));
    }

    /**
     * Lưu truyện mới
     */
    public function store(Request $request)
    {
        // Ép một số field rỗng về null (nếu cần)
        $this->castEmptyToNull($request, ['chapter_count', 'published_at']);

        $data = $request->validate($this->getValidationRules());

        $comic = new Comic();
        $comic->title         = $data['title'];
        $comic->slug          = $data['slug'] ?? Str::slug($data['title']);
        $comic->description   = $data['description'] ?? null;
        $comic->author        = $data['author'] ?? null;
        $comic->status        = $data['status'];

        $comic->chapter_count = $data['chapter_count'] ?? 0;
        $comic->published_at  = $data['published_at'] ?? null;

        // Các field thống kê để mặc định 0
        $comic->views         = 0;
        $comic->follows       = 0;
        $comic->rating        = 0;
        $comic->rating_count  = 0;

        // upload ảnh bìa
        $comic->cover_image = $this->uploadCoverImage($request, null);

        $comic->save();

        // sync categories (many-to-many)
        $comic->categories()->sync($data['category_ids']);

        return redirect()->route('comics.index')
            ->with('success', 'Truyện đã được tạo thành công.');
    }

    /**
     * Chi tiết một truyện
     */
    public function show(Comic $comic)
    {
        $comic->load('categories', 'chapters'); // nếu bạn có model Chapter
        return view('comics.show', compact('comic'));
    }

    /**
     * Form chỉnh sửa truyện
     */
    public function edit(Comic $comic)
    {
        $categories = Category::all();
        $selectedCategoryIds = $comic->categories()->pluck('categories.id')->toArray();

        return view('comics.edit', compact('comic', 'categories', 'selectedCategoryIds'));
    }

    /**
     * Cập nhật truyện
     */
    public function update(Request $request, Comic $comic)
    {
        $this->castEmptyToNull($request, ['chapter_count', 'published_at']);

        $validated = $request->validate($this->getValidationRules($comic));

        // update các field cơ bản
        $comic->title         = $validated['title'];
        $comic->slug          = $validated['slug'] ?? $comic->slug ?? Str::slug($validated['title']);
        $comic->description   = $validated['description'] ?? null;
        $comic->author        = $validated['author'] ?? null;
        $comic->status        = $validated['status'];
        $comic->chapter_count = $validated['chapter_count'] ?? $comic->chapter_count;
        $comic->published_at  = $validated['published_at'] ?? $comic->published_at;

        // xử lý ảnh bìa
        $comic->cover_image = $this->uploadCoverImage($request, $comic->cover_image);

        $comic->save();

        // Sync lại categories
        $comic->categories()->sync($validated['category_ids']);

        return redirect()->route('comics.index')
            ->with('success', 'Truyện đã được cập nhật thành công.');
    }

    /**
     * Xoá truyện
     */
    public function destroy(Comic $comic)
    {
        // Xóa ảnh bìa trên disk
        $this->deleteCoverImage($comic->cover_image);

        // Detach categories
        $comic->categories()->detach();

        $comic->delete();

        return redirect()->route('comics.index')
            ->with('success', 'Truyện đã được xóa thành công.');
    }
}
