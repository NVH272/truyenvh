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

        $categories = $query->orderBy('id', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.categories.index', compact('categories', 'search'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'status'      => 'required|in:0,1', // radio trong form của bạn
        ]);

        // Nếu slug trống → tự generate từ name
        $slug = $data['slug'] ?? '';
        if (trim($slug) === '') {
            $slug = Str::slug($data['name']);
        } else {
            // Người dùng có nhập, mình cũng normalize lại cho đẹp
            $slug = Str::slug($slug);
        }

        Category::create([
            'name'        => $data['name'],
            'slug'        => $slug,
            'description' => $data['description'] ?? null,
            'is_active'   => $data['status'], // map radio status → cột is_active
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
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
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'status'      => 'required|in:0,1',
        ]);

        $slug = $data['slug'] ?? '';
        if (trim($slug) === '') {
            $slug = Str::slug($data['name']);
        } else {
            $slug = Str::slug($slug);
        }

        $category->update([
            'name'        => $data['name'],
            'slug'        => $slug,
            'description' => $data['description'] ?? null,
            'is_active'   => $data['status'],
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }


    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
