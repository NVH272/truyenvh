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
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], // Tối đa 5MB
        ]);

        $emailChanged = $data['email'] !== $user->email;

        // Xử lý upload avatar (nếu có)
        if ($request->hasFile('avatar')) {
            // Xoá avatar cũ nếu có
            if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
                \Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        if ($emailChanged) {
            // Đổi email => reset trạng thái verify
            $user->forceFill([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'avatar'            => $data['avatar'] ?? $user->avatar,
                'email_verified_at' => null,
            ])->save();

            // Gửi lại email xác thực
            $user->sendEmailVerificationNotification();

            $msg = 'Cập nhật thành công! Vui lòng kiểm tra email mới và xác thực để sử dụng đầy đủ chức năng.';
        } else {
            // Không đổi email -> update bình thường
            $user->update($data);
            $msg = 'Cập nhật thành công!';
        }

        return redirect()
            ->route('user.profile.index')
            ->with('success', $msg);
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
            'password'         => ['required', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng!']);
        }

        $user->update(['password' => Hash::make($data['password'])]);

        return redirect()->route('user.profile.index')->with('success', 'Đổi mật khẩu thành công!');
    }
}
