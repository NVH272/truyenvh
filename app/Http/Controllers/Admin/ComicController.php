<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comic;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

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

            // Lưu vào storage/app/public/uploads/comics
            $file->storeAs('uploads/comics', $imageName, 'public');

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
            // Xóa từ storage/app/public/uploads/comics
            $storagePath = 'uploads/comics/' . $imageName;
            if (Storage::disk('public')->exists($storagePath)) {
                Storage::disk('public')->delete($storagePath);
            }

            // Xóa từ public/uploads/comics (nếu còn tồn tại từ code cũ)
            $publicPath = public_path('uploads/comics/' . $imageName);
            if (File::exists($publicPath)) {
                File::delete($publicPath);
            }
        }
    }

    /**
     * Danh sách truyện (ADMIN) + search + filter + paginate
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Comic::with('categories');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('author', 'LIKE', "%{$search}%");
            });
        }

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $comics = $query->orderByDesc('created_at')->paginate(12);

        // Đếm số truyện chờ duyệt
        $pendingCount = Comic::where('approval_status', 'pending')->count();

        return view('admin.comics.index', compact('comics', 'search', 'status', 'pendingCount'));
    }

    /**
     * Form tạo truyện (ADMIN)
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.comics.create', compact('categories'));
    }

    /**
     * Lưu truyện mới (ADMIN)
     */
    public function store(Request $request)
    {
        // Ép một số field rỗng về null (nếu cần)
        $this->castEmptyToNull($request, ['chapter_count', 'published_at']);

        $data = $request->validate($this->getValidationRules());

        $comic = new Comic();
        $comic->title = $data['title'];

        // Nếu user nhập slug thì dùng slug đó làm base, còn không thì dùng title
        $baseSlug = $data['slug'] ?: $data['title'];
        $comic->slug = $this->generateUniqueSlug($baseSlug, null);
        $comic->description   = $data['description'] ?? null;
        $comic->author        = $data['author'] ?? null;
        $comic->status        = $data['status'];

        $comic->chapter_count = $data['chapter_count'] ?? 0;
        $comic->published_at  = $data['published_at'] ?? null;

        // Gán người tạo truyện
        $comic->created_by    = Auth::id();

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

        return redirect()->route('admin.comics.index')
            ->with('success', 'Truyện đã được tạo thành công.');
    }

    /**
     * (OPTIONAL) Chi tiết truyện – nếu muốn trang chi tiết trong admin
     */
    public function show(Comic $comic)
    {
        $comic->load('categories', 'chapters'); // nếu có model Chapter
        return view('admin.comics.show', compact('comic'));
    }

    /**
     * Form chỉnh sửa truyện (ADMIN)
     */
    public function edit(Comic $comic)
    {
        $categories = Category::all();
        $selectedCategoryIds = $comic->categories()->pluck('categories.id')->toArray();

        return view('admin.comics.edit', compact('comic', 'categories', 'selectedCategoryIds'));
    }

    /**
     * Cập nhật truyện (ADMIN)
     */
    public function update(Request $request, Comic $comic)
    {
        $this->castEmptyToNull($request, ['chapter_count', 'published_at']);

        $validated = $request->validate($this->getValidationRules($comic));

        // update các field cơ bản
        $comic->title = $validated['title'];

        // baseSlug ưu tiên theo thứ tự: slug user nhập > slug hiện tại > title
        $baseSlug = $validated['slug'] ?: ($comic->slug ?: $validated['title']);
        $comic->slug = $this->generateUniqueSlug($baseSlug, $comic->id);
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

        return redirect()->route('admin.comics.index')
            ->with('success', 'Truyện đã được cập nhật thành công.');
    }

    /**
     * Xoá truyện (ADMIN)
     */
    public function destroy(Comic $comic)
    {
        // Xóa ảnh bìa trên disk
        $this->deleteCoverImage($comic->cover_image);

        // Detach categories
        $comic->categories()->detach();

        $comic->delete();

        return redirect()->route('admin.comics.index')
            ->with('success', 'Truyện đã được xóa thành công.');
    }

    /**
     * Tạo slug không trùng trong bảng comics
     */
    private function generateUniqueSlug(string $baseSlug, ?int $ignoreId = null): string
    {
        $slug = Str::slug($baseSlug);          // chuẩn hóa
        $originalSlug = $slug;
        $counter = 1;

        $query = Comic::query();

        // Nếu là update thì bỏ qua chính nó
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        // Nếu đã tồn tại thì thêm -1, -2, ...
        while ($query->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
            // reset query để tránh where chồng chất
            $query = Comic::query();
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
        }

        return $slug;
    }

    // Danh sách truyện của user hiện tại (ADMIN + POSTER)
    public function myComics()
    {
        $user = Auth::user();

        if (!in_array($user->role, ['admin', 'poster'])) {
            abort(403, 'Bạn không có quyền xem trang này.');
        }

        $comics = Comic::where('created_by', $user->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('user.my_comics.index', compact('comics'));
    }

    public function pending()
    {
        $comics = Comic::where('approval_status', 'pending')
            ->with('creator', 'categories')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.comics.pending', compact('comics'));
    }

    public function approve(Comic $comic)
    {
        // Chỉ cho phép duyệt truyện đang ở trạng thái pending
        if ($comic->approval_status !== 'pending') {
            return back()->with('error', 'Truyện này không ở trạng thái chờ duyệt.');
        }

        $comic->approval_status = 'approved';
        $comic->approved_by     = Auth::id();
        $comic->approved_at     = now();
        $comic->rejection_reason = null;
        $comic->save();

        return back()->with('success', 'Đã phê duyệt truyện: ' . $comic->title);
    }

    public function reject(Request $request, Comic $comic)
    {
        // Chỉ cho phép từ chối truyện đang ở trạng thái pending
        if ($comic->approval_status !== 'pending') {
            return back()->with('error', 'Truyện này không ở trạng thái chờ duyệt.');
        }

        $request->validate([
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $comic->approval_status = 'rejected';
        $comic->approved_by     = Auth::id();
        $comic->approved_at     = now();
        $comic->rejection_reason = $request->input('reason');
        $comic->save();

        return back()->with('success', 'Đã từ chối truyện: ' . $comic->title);
    }

    public function reviewHistory()
    {
        $comics = Comic::whereIn('approval_status', ['approved', 'rejected'])
            ->with(['creator', 'categories', 'approver']) // approver mình sẽ nói bên dưới
            ->orderByDesc('approved_at')
            ->paginate(20);

        return view('admin.comics.review_history', compact('comics'));
    }
}
