<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Nhớ import Storage

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $role   = $request->input('role');

        $query = User::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($role)) {
            $query->where('role', $role);
        }

        $users = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users', 'search', 'role'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        // Chuẩn hóa email: trim và lowercase TRƯỚC khi validate
        $email = strtolower(trim($request->email));
        $request->merge(['email' => $email]);
        
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'  => ['required', 'string', 'min:6'],
            'role'      => ['required', 'in:admin,user,poster'],
            'is_active' => ['nullable', 'boolean'],
            'avatar'    => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'], // Tối đa 5MB
        ], [
            'avatar.image' => 'File phải là hình ảnh hợp lệ.',
            'avatar.mimes' => 'Định dạng ảnh không hợp lệ. Chỉ chấp nhận: JPG, PNG, GIF, WEBP.',
            'avatar.max'   => 'Dung lượng ảnh vượt quá 5MB. Vui lòng chọn ảnh nhỏ hơn.',
        ]);

        $data['password']  = Hash::make($data['password']);
        $data['is_active'] = $request->boolean('is_active', true);

        // Xử lý upload avatar
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'Thêm người dùng thành công.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // Chuẩn hóa email: trim và lowercase TRƯỚC khi validate
        $email = strtolower(trim($request->email));
        $request->merge(['email' => $email]);
        
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password'  => ['nullable', 'string', 'min:6'],
            'role'      => ['required', 'in:admin,user,poster'],
            'is_active' => ['nullable', 'boolean'],
            'avatar'    => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'], // Tối đa 5MB
        ], [
            'avatar.image' => 'File phải là hình ảnh hợp lệ.',
            'avatar.mimes' => 'Định dạng ảnh không hợp lệ. Chỉ chấp nhận: JPG, PNG, GIF, WEBP.',
            'avatar.max'   => 'Dung lượng ảnh vượt quá 5MB. Vui lòng chọn ảnh nhỏ hơn.',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['is_active'] = $request->boolean('is_active', true);

        // Xử lý upload avatar mới
        if ($request->hasFile('avatar')) {
            // Xóa ảnh cũ nếu tồn tại và không phải là null
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật người dùng thành công.');
    }

    public function destroy(User $user)
    {
        if (Auth::id() === $user->id) {
            return back()->with('error', 'Không thể xóa tài khoản đang đăng nhập.');
        }

        // Xóa avatar khi xóa user để dọn rác
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Xóa người dùng thành công.');
    }

    public function toggleActive(User $user)
    {
        if (Auth::id() === $user->id) {
            return back()->with('error', 'Không thể khóa tài khoản đang đăng nhập.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('success', 'Cập nhật trạng thái tài khoản thành công.');
    }
}
