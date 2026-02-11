<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        // 1. Loại bỏ khoảng trắng thừa ở đầu/cuối
        $search = trim($request->input('q'));
        $sort = $request->input('sort', 'name_asc');

        $query = Category::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {

                // --- CẤP ĐỘ 1: Tìm chính xác cụm từ (Độ ưu tiên cao nhất) ---
                $q->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('description', 'LIKE', '%' . $search . '%');

                // --- CẤP ĐỘ 2: Tìm không dấu thông qua Slug ---
                $slugSearch = Str::slug($search);
                if (!empty($slugSearch)) {
                    $q->orWhere('slug', 'LIKE', '%' . $slugSearch . '%');
                }
            });
        }

        switch ($sort) {
        case 'latest':
            $query->orderBy('id', 'desc');
            break;
        case 'name_asc':
            $query->orderBy('name', 'asc');
            break;
        case 'name_desc':
            $query->orderBy('name', 'desc');
            break;
        case 'oldest':
            $query->orderBy('created_at', 'asc');
            break;
        default:
            $query->orderBy('name', 'asc');
            break;
    }

        $categories = $query
            ->paginate(10)
            ->withQueryString();

        return view('admin.categories.index', compact('categories', 'search', 'sort'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        // Kiểm tra tên đã tồn tại trước
        $name = trim($request->input('name'));
        if (Category::where('name', $name)->exists()) {
            return back()
                ->withInput()
                ->withErrors(['name' => 'Tên thể loại "' . $name . '" đã tồn tại. Vui lòng chọn tên khác.']);
        }

        $data = $request->validate(
            [
                'name'        => 'required|string|max:255|unique:categories,name',
                'slug'        => 'nullable|string|max:255|unique:categories,slug',
                'description' => 'nullable|string',
                'status'      => 'required|in:0,1',
            ],
            [
                'name.required' => 'Tên thể loại là bắt buộc.',
                'name.unique' => 'Tên thể loại đã tồn tại. Vui lòng chọn tên khác.',
                'slug.unique' => 'Slug đã tồn tại. Vui lòng nhập slug khác.',
                'status.required' => 'Trạng thái là bắt buộc.',
                'status.in' => 'Trạng thái không hợp lệ.',
            ]
        );

        // Nếu slug trống → tự generate từ name
        $slug = $data['slug'] ?? '';
        if (trim($slug) === '') {
            $slug = Str::slug($data['name']);
            
            // Kiểm tra slug tự generate có bị trùng không
            if (Category::where('slug', $slug)->exists()) {
                return back()
                    ->withInput()
                    ->withErrors(['name' => 'Thể loại với tên này đã tồn tại (slug tự động tạo bị trùng). Vui lòng nhập slug thủ công.']);
            }
        } else {
            // Người dùng có nhập, normalize lại cho đẹp
            $slug = Str::slug($slug);
        }

        try {
        Category::create([
            'name'        => $data['name'],
            'slug'        => $slug,
            'description' => $data['description'] ?? null,
                'is_active'   => $data['status'],
        ]);

        return redirect()->route('admin.categories.index')
                ->with('success', 'Thể loại đã được tạo thành công.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Có lỗi xảy ra khi tạo thể loại: ' . $e->getMessage()]);
        }
    }


    public function show(Category $category)
    {
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $name = trim($request->input('name'));
        
        // Kiểm tra nếu tên không thay đổi thì không cần validate unique
        $nameChanged = $name !== $category->name;
        
        // Nếu tên đã thay đổi, kiểm tra xem tên mới có bị trùng không
        if ($nameChanged) {
            if (Category::where('name', $name)->where('id', '!=', $category->id)->exists()) {
                return back()
                    ->withInput()
                    ->withErrors(['name' => 'Tên thể loại "' . $name . '" đã tồn tại. Vui lòng chọn tên khác.']);
            }
        }

        // Validation rules: chỉ validate unique nếu tên thay đổi
        $validationRules = [
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'required|in:0,1',
        ];

        // Chỉ thêm unique rule nếu tên thay đổi
        if ($nameChanged) {
            $validationRules['name'] .= '|unique:categories,name,' . $category->id;
        }

        // Kiểm tra slug unique (bỏ qua chính nó)
        $slug = trim($request->input('slug', ''));
        if (!empty($slug)) {
            $validationRules['slug'] .= '|unique:categories,slug,' . $category->id;
        }

        $data = $request->validate(
            $validationRules,
            [
                'name.required' => 'Tên thể loại là bắt buộc.',
                'name.unique' => 'Tên thể loại đã tồn tại. Vui lòng chọn tên khác.',
                'slug.unique' => 'Slug đã tồn tại. Vui lòng nhập slug khác.',
                'status.required' => 'Trạng thái là bắt buộc.',
                'status.in' => 'Trạng thái không hợp lệ.',
            ]
        );

        // Nếu slug trống → tự generate từ name
        if (trim($slug) === '') {
            $slug = Str::slug($data['name']);
            
            // Kiểm tra slug tự generate có bị trùng không (bỏ qua chính nó)
            $existingCategory = Category::where('slug', $slug)->where('id', '!=', $category->id)->first();
            if ($existingCategory) {
                return back()
                    ->withInput()
                    ->withErrors(['name' => 'Thể loại với tên này đã tồn tại (slug tự động tạo bị trùng). Vui lòng nhập slug thủ công.']);
            }
        } else {
            // Người dùng có nhập, normalize lại cho đẹp
            $slug = Str::slug($slug);
        }

        try {
        $category->update([
            'name'        => $data['name'],
            'slug'        => $slug,
            'description' => $data['description'] ?? null,
            'is_active'   => $data['status'],
        ]);

        return redirect()->route('admin.categories.index')
                ->with('success', 'Thể loại đã được cập nhật thành công.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Có lỗi xảy ra khi cập nhật thể loại: ' . $e->getMessage()]);
        }
    }


    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
