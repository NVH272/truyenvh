@extends('layouts.admin')

@section('title', 'Thêm Thành viên Mới')
@section('header', 'Tạo Tài khoản')

@section('content')
<div class="max-w-3xl mx-auto">
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-sm text-slate-400 hover:text-orange-500 mb-6 transition group">
        <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i> Quay lại danh sách
    </a>

    <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-xl overflow-hidden">
        <div class="border-b border-slate-700 px-6 py-4 bg-slate-900/30">
            <h3 class="font-bold text-white brand-font text-lg">Thông tin Tài khoản</h3>
            <p class="text-xs text-slate-500 mt-1">Tạo tài khoản mới để truy cập hệ thống.</p>
        </div>

        <!-- Thêm enctype="multipart/form-data" -->
        <form action="{{ route('admin.users.store') }}" method="POST" class="p-6 space-y-6" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Avatar Upload -->
                <div class="md:col-span-2">
                    <label class="text-xs font-bold text-slate-300 uppercase tracking-wider block mb-2">Ảnh đại diện (Tùy chọn)</label>
                    <div class="flex items-center gap-4">
                        <div class="shrink-0">
                            <!-- Hiển thị placeholder mặc định khi chưa upload -->
                            <div id="avatar-preview-placeholder" class="h-16 w-16 rounded-full bg-slate-700 border-2 border-slate-600 flex items-center justify-center text-slate-400">
                                <i class="fas fa-user text-2xl"></i>
                            </div>
                            <img id="avatar-preview" src="" alt="Preview" class="h-16 w-16 rounded-full border-2 border-slate-600 object-cover hidden">
                        </div>
                        <div class="flex-1">
                            <input type="file" name="avatar" id="avatar-input" class="block w-full text-sm text-slate-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-xs file:font-semibold
                                file:bg-orange-600 file:text-white
                                hover:file:bg-orange-700
                                cursor-pointer transition
                            " accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" />
                            <p class="text-[10px] text-slate-500 mt-1">JPG, PNG, GIF, WEBP • Tối đa 5MB</p>
                            <!-- Thông báo lỗi màu đỏ -->
                            <div id="avatar-error" class="hidden">
                                <span class="text-red-500 text-xs font-bold block mt-1"></span>
                            </div>
                            @error('avatar')
                                <span class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Name -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-300 uppercase tracking-wider">Họ và Tên <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="admin-input w-full px-4 py-2.5 rounded-lg text-sm bg-slate-900/50 border-slate-600 focus:border-orange-500 text-white placeholder-slate-600 transition" placeholder="Nhập tên hiển thị" required>
                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-300 uppercase tracking-wider">Email (D-Mail) <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" class="admin-input w-full px-4 py-2.5 rounded-lg text-sm bg-slate-900/50 border-slate-600 focus:border-orange-500 text-white placeholder-slate-600 transition" placeholder="email@example.com" required>
                    @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-300 uppercase tracking-wider">Mật khẩu <span class="text-red-500">*</span></label>
                    <input type="password" name="password" class="admin-input w-full px-4 py-2.5 rounded-lg text-sm bg-slate-900/50 border-slate-600 focus:border-orange-500 text-white placeholder-slate-600 transition" placeholder="*******" required>
                    @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Role -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-300 uppercase tracking-wider">Vai trò (Role) <span class="text-red-500">*</span></label>
                    <select name="role" class="admin-input w-full px-4 py-2.5 rounded-lg text-sm bg-slate-900/50 border-slate-600 focus:border-orange-500 text-white transition cursor-pointer appearance-none">
                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User (Thành viên)</option>
                        <option value="poster" {{ old('role') == 'poster' ? 'selected' : '' }}>Poster (Người đăng)</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin (Quản trị viên)</option>
                    </select>
                    @error('role') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Is Active -->
            <div class="space-y-3 pt-2">
                <label class="flex items-center gap-3 cursor-pointer group p-3 bg-slate-900/30 rounded-lg border border-slate-700/50 hover:border-slate-600 transition select-none">
                    <div class="relative flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1" class="peer sr-only" checked>
                        <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-orange-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-slate-300 group-hover:text-white transition block">Kích hoạt tài khoản</span>
                        <span class="text-[10px] text-slate-500 block">Cho phép người dùng đăng nhập ngay lập tức.</span>
                    </div>
                </label>
            </div>

            <!-- Actions -->
            <div class="pt-6 border-t border-slate-700 flex items-center gap-4">
                <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-lg shadow-orange-900/30 transition transform active:scale-95 flex items-center">
                    <i class="fas fa-save mr-2"></i> Lưu Thành viên
                </button>
                <a href="{{ route('admin.users.index') }}" class="bg-transparent border border-slate-600 hover:bg-slate-700 text-slate-300 hover:text-white px-6 py-2.5 rounded-lg text-sm font-bold transition text-center">
                    Hủy bỏ
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const avatarInput = document.getElementById('avatar-input');
        const avatarPreview = document.getElementById('avatar-preview');
        const avatarPlaceholder = document.getElementById('avatar-preview-placeholder');
        const avatarError = document.getElementById('avatar-error');
        const avatarErrorText = avatarError.querySelector('span');

        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            // Reset error
            avatarError.classList.add('hidden');
            avatarErrorText.textContent = '';

            if (!file) {
                // Không chọn file, hiển thị placeholder
                avatarPreview.classList.add('hidden');
                avatarPlaceholder.classList.remove('hidden');
                return;
            }

            // Kiểm tra định dạng
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                avatarErrorText.textContent = '❌ Định dạng ảnh không hợp lệ. Chỉ chấp nhận: JPG, PNG, GIF, WEBP';
                avatarError.classList.remove('hidden');
                avatarInput.value = '';
                return;
            }

            // Kiểm tra dung lượng (5MB = 5 * 1024 * 1024 bytes)
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSize) {
                avatarErrorText.textContent = '❌ Dung lượng ảnh vượt quá 5MB. Vui lòng chọn ảnh nhỏ hơn.';
                avatarError.classList.remove('hidden');
                avatarInput.value = '';
                return;
            }

            // Hiển thị preview
            const reader = new FileReader();
            reader.onload = function(e) {
                avatarPreview.src = e.target.result;
                avatarPreview.classList.remove('hidden');
                avatarPlaceholder.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        });
    });
</script>
@endsection