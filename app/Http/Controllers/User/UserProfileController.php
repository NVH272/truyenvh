<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class UserProfileController extends Controller
{
    // Trang cá nhân (chỉ hiển thị)
    public function index()
    {
        $user = Auth::user();
        return view('user.profile.index', compact('user'));
    }

    // Form chỉnh sửa thông tin
    public function editInfo()
    {
        $user = Auth::user();
        return view('user.profile.edit', compact('user'));
    }

    // Xử lý cập nhật thông tin + avatar
    public function updateInfo(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'email'  => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);

        // Cập nhật tên + email
        $user->name  = $data['name'];
        $user->email = $data['email'];

        // Nếu có upload avatar mới
        if ($request->hasFile('avatar')) {
            // Xóa ảnh cũ nếu có & tồn tại
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Lưu ảnh mới vào storage/app/public/avatars
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path; // cột avatar trong bảng users
        }

        $user->save();

        return redirect()->route('user.profile.index')->with('success', 'Cập nhật thông tin thành công!');
    }

    // Form đổi mật khẩu
    public function editPassword()
    {
        return view('user.profile.changePassword');
    }

    // Xử lý đổi mật khẩu
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'min:6', 'confirmed'],
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng!']);
        }

        $user->update(['password' => Hash::make($data['password'])]);

        return redirect()->route('user.profile.index')->with('success', 'Đổi mật khẩu thành công!');
    }
}
