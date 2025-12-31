<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Comic;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserComicController extends Controller
{
    // Danh sách truyện do chính user đăng
    public function index()
    {
        $user = Auth::user();

        $comics = Comic::where('created_by', $user->id)
            ->latest()
            ->paginate(24);

        return view('user.my-comics.index', compact('comics'));
    }

    // Form thêm truyện (user)
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('user.my-comics.create', compact('categories'));
    }

    // Lưu truyện mới do user đăng
    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $comic = new Comic();
        $comic->title         = $data['title'];
        $comic->slug          = $this->generateUniqueSlug($data['slug'] ?: $data['title']);
        $comic->description   = $data['description'] ?? null;
        $comic->author        = $data['author'] ?? null;
        $comic->status        = $data['status'];
        // $comic->chapter_count = isset($data['chapter_count'])
        //     ? (int) $data['chapter_count']
        //     : 0;
        $comic->published_at  = $data['published_at'] ?? null;

        // Thống kê mặc định
        $comic->views         = 0;
        $comic->follows       = 0;
        $comic->rating        = 0;
        $comic->rating_count  = 0;

        // Chủ sở hữu
        $comic->created_by = Auth::id();

        // Trạng thái duyệt
        $user = Auth::user();
        if ($user->role === 'poster') {
            // Poster phải chờ admin duyệt
            $comic->approval_status = 'pending';
        } else {
            // Admin tự đăng thì coi như đã duyệt
            $comic->approval_status = 'approved';
        }

        // Ảnh bìa
        $comic->cover_image = $this->uploadCoverImage($request, null);

        $comic->save();

        $comic->categories()->sync($data['category_ids']);

        return redirect()
            ->route('user.my-comics.index')
            ->with('success', $user->role === 'poster'
                ? 'Truyện đã được gửi, vui lòng chờ admin xét duyệt.'
                : 'Truyện của bạn đã được tạo thành công.');
    }

    // Form sửa truyện
    public function edit(Comic $comic)
    {
        $this->authorizeComic($comic);

        $categories = Category::orderBy('name')->get();
        $selectedCategories = $comic->categories()->pluck('categories.id')->toArray();

        return view('user.my-comics.edit', compact('comic', 'categories', 'selectedCategories'));
    }

    // Cập nhật truyện
    public function update(Request $request, Comic $comic)
    {
        $this->authorizeComic($comic);

        $data = $this->validateData($request, $comic->id);

        $comic->title         = $data['title'];
        $comic->slug          = $this->generateUniqueSlug($data['slug'] ?: $data['title'], $comic->id);
        $comic->description   = $data['description'] ?? null;
        $comic->author        = $data['author'] ?? null;
        $comic->status        = $data['status'];
        // $comic->chapter_count = isset($data['chapter_count'])
        //     ? (int) $data['chapter_count']
        //     : 0;
        $comic->published_at  = $data['published_at'] ?? null;

        // Ảnh bìa mới (nếu có)
        $comic->cover_image = $this->uploadCoverImage($request, $comic->cover_image);

        $comic->save();
        $comic->categories()->sync($data['category_ids']);

        return redirect()
            ->route('user.my-comics.index')
            ->with('success', 'Truyện của bạn đã được cập nhật.');
    }

    // Xoá truyện
    public function destroy(Comic $comic)
    {
        $this->authorizeComic($comic);

        // Xóa ảnh bìa nếu có
        if ($comic->cover_image) {
            $imagePath = 'uploads/comics/' . $comic->cover_image;
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        $comic->delete();

        return redirect()
            ->route('user.my-comics.index')
            ->with('success', 'Truyện của bạn đã được xoá.');
    }

    /* ================== HELPER METHODS ================== */

    // Chỉ cho phép: admin & poster, và phải là người tạo (kể cả admin)
    protected function authorizeComic(Comic $comic)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['admin', 'poster'])) {
            abort(403, 'Bạn không có quyền thao tác với truyện.');
        }

        if ($comic->created_by !== $user->id) {
            abort(403, 'Bạn chỉ được sửa/xoá truyện do chính bạn đăng.');
        }
    }

    protected function validateData(Request $request, $ignoreId = null)
    {
        $ruleSlugUnique = 'unique:comics,slug';
        if ($ignoreId) {
            $ruleSlugUnique .= ',' . $ignoreId;
        }

        // Đảm bảo category_ids luôn là array (nếu không có thì là mảng rỗng)
        if (!$request->has('category_ids')) {
            $request->merge(['category_ids' => []]);
        }

        return $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'slug'          => ['nullable', 'string', 'max:255', $ruleSlugUnique],
            'author'        => ['nullable', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'status'        => ['required', 'in:ongoing,completed,dropped'],
            // 'chapter_count' => ['nullable', 'numeric', 'min:0'],
            'published_at'  => ['nullable', 'date'],
            'category_ids'  => ['required', 'array', 'min:1'],
            'category_ids.*' => ['required', 'integer', 'exists:categories,id'],
            'cover_image'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:4096'],
        ], [
            'category_ids.required' => 'Vui lòng chọn ít nhất một thể loại.',
            'category_ids.min' => 'Vui lòng chọn ít nhất một thể loại.',
        ]);
    }

    protected function generateUniqueSlug(string $base, $ignoreId = null): string
    {
        $slug = Str::slug($base);

        $original = $slug;
        $i = 1;

        while (
            Comic::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $original . '-' . $i++;
        }

        return $slug;
    }

    protected function uploadCoverImage(Request $request, ?string $oldImageName): ?string
    {
        if (!$request->hasFile('cover_image')) {
            return $oldImageName;
        }

        // Xoá ảnh cũ nếu có
        if ($oldImageName) {
            $oldPath = 'uploads/comics/' . $oldImageName;
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        // Lưu ảnh mới vào uploads/comics với tên file unique
        $file = $request->file('cover_image');
        $imageName = uniqid('comic_') . '.' . $file->getClientOriginalExtension();

        // Lưu vào storage/app/public/uploads/comics
        $file->storeAs('uploads/comics', $imageName, 'public');

        // Chỉ trả về tên file (không có đường dẫn)
        return $imageName;
    }
}
