@extends('layouts.admin')

@section('title', 'Chỉnh sửa Thành viên')
@section('header', 'Cập nhật Tài khoản')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Nav & Back Button -->
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-sm text-slate-400 hover:text-blue-500 transition group">
            <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i> Quay lại danh sách
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-xl overflow-hidden relative">
        <!-- Decor Stripe (Blue for Edit) -->
        <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>

        <div class="border-b border-slate-700 px-6 py-4 bg-slate-900/30 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-white brand-font text-lg">
                    Sửa thông tin: <span class="text-blue-400">{{ $user->name }}</span>
                </h3>
                <p class="text-xs text-slate-500 mt-1 font-mono-tech">ID: #{{ str_pad($user->id, 3, '0', STR_PAD_LEFT) }}</p>
            </div>

            <!-- Status Badge (Display Only) -->
            @if($user->is_active)
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-500/10 text-green-500 border border-green-500/20">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5 animate-pulse"></span> Hoạt động
            </span>
            @else
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-700 text-gray-400 border border-gray-600">
                <i class="fas fa-lock mr-1.5 text-[10px]"></i> Đã khóa
            </span>
            @endif
        </div>

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="p-6 space-y-8" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Avatar Upload Section (Centered & Hover Effect) -->
            <div class="flex flex-col items-center justify-center -mt-2 mb-6">
                <div class="relative group cursor-pointer w-32 h-32">
                    <!-- Avatar Image -->
                    <img class="w-full h-full object-cover rounded-full border-4 border-slate-700 shadow-2xl group-hover:border-blue-500 transition-all duration-300"
                        src="{{ $user->avatar_url }}"
                        alt="{{ $user->name }}"
                        id="avatar-preview">

                    <!-- Hover Overlay & Icon -->
                    <label for="avatar-upload" class="absolute inset-0 bg-black/50 rounded-full flex flex-col items-center justify-center opacity-0 group-hover:opacity-75 transition-all duration-300 cursor-pointer backdrop-blur-sm">
                        <i class="fas fa-camera text-white text-2xl mb-1 transform group-hover:scale-110 transition-transform"></i>
                        <span class="text-white text-[10px] font-bold uppercase tracking-wider">Đổi ảnh</span>
                    </label>

                    <!-- Hidden File Input -->
                    <input type="file" name="avatar" id="avatar-upload" class="hidden" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" onchange="previewAvatar(this)">
                </div>
                <!-- Helper text below avatar -->
                <p class="text-[10px] text-slate-500 mt-3 font-mono-tech">JPG, PNG, GIF, WEBP • Tối đa 5MB</p>
                <!-- Thông báo lỗi màu đỏ -->
                <div id="avatar-error" class="hidden mt-2">
                    <span class="text-red-500 text-xs font-bold block"></span>
                </div>
                @error('avatar')
                <span class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Grid Layout giống trang Create -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-300 uppercase tracking-wider">Họ và Tên</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="admin-input w-full px-4 py-2.5 rounded-lg text-sm bg-slate-900/50 border-slate-600 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-white placeholder-slate-600 transition" required>
                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-300 uppercase tracking-wider">Email (D-Mail)</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="admin-input w-full px-4 py-2.5 rounded-lg text-sm bg-slate-900/50 border-slate-600 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-white placeholder-slate-600 transition" required>
                    @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-300 uppercase tracking-wider">Mật khẩu mới</label>
                    <input type="password" name="password" class="admin-input w-full px-4 py-2.5 rounded-lg text-sm bg-slate-900/50 border-slate-600 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-white placeholder-slate-600 transition" placeholder="Để trống nếu không đổi">
                    <p class="text-[10px] text-slate-500 italic mt-1">Chỉ nhập khi bạn muốn thay đổi mật khẩu hiện tại.</p>
                    @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Role -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-300 uppercase tracking-wider">Vai trò</label>
                    <select name="role" class="admin-input w-full px-4 py-2.5 rounded-lg text-sm bg-slate-900/50 border-slate-600 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-white transition cursor-pointer appearance-none">
                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User (Thành viên)</option>
                        <option value="poster" {{ old('role', $user->role) == 'poster' ? 'selected' : '' }}>Poster (Người đăng)</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin (Quản trị viên)</option>
                    </select>
                    @error('role') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Is Active Toggle Switch -->
            <div class="space-y-3 pt-2 border-t border-slate-700/50 mt-2">
                @if(Auth::id() !== $user->id)
                <label class="flex items-center gap-3 cursor-pointer group p-3 bg-slate-900/30 rounded-lg border border-slate-700/50 hover:border-slate-600 transition select-none">
                    <div class="relative flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1" class="peer sr-only" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-slate-300 group-hover:text-white transition block">Trạng thái hoạt động</span>
                        <span class="text-[10px] text-slate-500 block">Tắt để tạm khóa tài khoản này.</span>
                    </div>
                </label>
                @else
                <div class="p-3 bg-blue-500/10 rounded-lg border border-blue-500/20 flex items-center gap-3">
                    <i class="fas fa-user-shield text-blue-500 text-xl"></i>
                    <div>
                        <span class="text-sm font-bold text-blue-400 block">Tài khoản Quản trị viên</span>
                        <span class="text-[10px] text-slate-400 block">Bạn đang đăng nhập bằng tài khoản này. Không thể tự vô hiệu hóa.</span>
                    </div>
                </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="pt-6 border-t border-slate-700 flex items-center gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-lg shadow-blue-900/30 transition transform active:scale-95 flex items-center justify-center w-full md:w-auto">
                    <i class="fas fa-check-circle mr-2"></i> Cập nhật Thông tin
                </button>
                <a href="{{ route('admin.users.index') }}" class="bg-transparent border border-slate-600 hover:bg-slate-700 text-slate-300 hover:text-white px-6 py-2.5 rounded-lg text-sm font-bold transition text-center w-full md:w-auto">
                    Hủy bỏ
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    // Script để preview ảnh và validate
    function previewAvatar(input) {
        const avatarPreview = document.getElementById('avatar-preview');
        const avatarError = document.getElementById('avatar-error');
        const avatarErrorText = avatarError.querySelector('span');

        // Reset error
        avatarError.classList.add('hidden');
        avatarErrorText.textContent = '';

        if (!input.files || !input.files[0]) {
            return;
        }

        const file = input.files[0];

        // Kiểm tra định dạng
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            avatarErrorText.textContent = '❌ Định dạng ảnh không hợp lệ. Chỉ chấp nhận: JPG, PNG, GIF, WEBP';
            avatarError.classList.remove('hidden');
            input.value = '';
            return;
        }

        // Kiểm tra dung lượng (5MB = 5 * 1024 * 1024 bytes)
        const maxSize = 5 * 1024 * 1024; // 5MB
        if (file.size > maxSize) {
            avatarErrorText.textContent = '❌ Dung lượng ảnh vượt quá 5MB. Vui lòng chọn ảnh nhỏ hơn.';
            avatarError.classList.remove('hidden');
            input.value = '';
            return;
        }

        // Hiển thị preview nếu hợp lệ
        var reader = new FileReader();
        reader.onload = function(e) {
            avatarPreview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
</script>
@endsection