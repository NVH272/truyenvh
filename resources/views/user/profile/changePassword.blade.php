@extends('layouts.app')

@section('title', 'Đổi mật khẩu')

@section('content')
<div class="min-h-[calc(100vh-140px)] flex items-center justify-center px-4 py-10">

    <!-- Main Container Card -->
    <div class="w-full max-w-[1100px] bg-slate-900 rounded-lg shadow-2xl overflow-hidden flex flex-col md:flex-row min-h-[520px]">

        <!-- LEFT COLUMN: FORM -->
        <div class="w-full md:w-7/12 px-8 py-8 md:px-12 md:py-10 relative flex flex-col bg-slate-950 text-slate-100">

            <!-- Decorative Background Gear -->
            <div class="absolute bottom-0 left-0 opacity-5 pointer-events-none">
                <i class="fas fa-cog text-9xl"></i>
            </div>

            <!-- HEADER -->
            <div class="flex justify-between items-start mb-8 border-b border-white/5 pb-4 relative z-10">
                <h2 class="text-xl md:text-2xl font-bold uppercase tracking-wide flex items-center gap-2">
                    <span class="text-orange-500 font-mono">>_</span>
                    Thay đổi mật khẩu
                </h2>
                <div class="border border-orange-500/40 text-orange-500 text-[10px] font-mono px-2 py-1 rounded bg-orange-500/5 tracking-[0.25em]">
                    SECURE <span class="animate-pulse">●</span>
                </div>
            </div>

            <!-- FORM CONTENT -->
            <div class="relative z-10">
                @if($errors->any())
                <div class="mb-6 bg-red-950/60 border border-red-500/40 text-red-200 px-4 py-3 rounded-lg text-sm flex items-start gap-3">
                    <i class="fas fa-times-circle mt-0.5"></i>
                    <div>
                        <span class="font-bold block mb-1">Đã xảy ra lỗi:</span>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif

                <form action="{{ route('user.profile.updatePassword') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Current Password -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-slate-200">
                            Mật khẩu hiện tại <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="current_password"
                                class="w-full px-4 py-3 rounded bg-slate-900 border border-slate-700 text-slate-100 text-sm
                                          focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition-all placeholder-slate-500"
                                placeholder="••••••••" required>
                            <i class="fas fa-lock absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm"></i>
                        </div>
                    </div>

                    <!-- New Password Group -->
                    <div class="p-4 md:p-5 bg-slate-900/70 rounded-xl border border-slate-800/80">
                        <!-- 2 INPUTS -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-2">
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                    Mật khẩu mới
                                </label>
                                <div class="relative">
                                    <input type="password" name="password"
                                        class="w-full px-4 py-2.5 rounded bg-slate-950 border border-slate-700 text-slate-100 text-sm
                                                  focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none placeholder-slate-500"
                                        placeholder="Nhập mật khẩu mới" required>
                                    <i class="fas fa-key absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                    Xác nhận mật khẩu
                                </label>
                                <div class="relative">
                                    <input type="password" name="password_confirmation"
                                        class="w-full px-4 py-2.5 rounded bg-slate-950 border border-slate-700 text-slate-100 text-sm
                                                  focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none placeholder-slate-500"
                                        placeholder="Nhập lại mật khẩu mới" required>
                                    <i class="fas fa-check-double absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <!-- 2 BOX TIPS ĐẶT DƯỚI 2 INPUT -->
                        <div class="mt-6 grid grid-cols-1 xl:grid-cols-2 gap-4">

                            <!-- Strong Password Tip -->
                            <div class="bg-slate-900/80 rounded-xl p-4 shadow-lg shadow-slate-900/40 border border-slate-800/60">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-shield-alt text-sky-400 mt-0.5"></i>
                                    <div>
                                        <h3 class="text-sm font-semibold text-slate-100">Mật khẩu mạnh</h3>
                                        <p class="text-xs text-slate-400 mt-1">
                                            Ít nhất 8 ký tự, gồm chữ hoa, chữ thường, số và ký tự đặc biệt.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Notice Tip -->
                            <div class="bg-slate-900/80 rounded-xl p-4 shadow-lg shadow-slate-900/40 border border-slate-800/60">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-exclamation-triangle text-orange-400 mt-0.5"></i>
                                    <div>
                                        <h3 class="text-sm font-semibold text-slate-100">Lưu ý</h3>
                                        <p class="text-xs text-slate-400 mt-1">
                                            Sau khi đổi mật khẩu thành công, bạn có thể sẽ cần đăng nhập lại trên các thiết bị khác.
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="mt-4 text-[10px] text-slate-400 italic flex items-center gap-1">
                            <i class="fas fa-info-circle text-slate-500"></i>
                            <span>Mật khẩu mới phải khác mật khẩu cũ.</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="pt-4 flex items-center justify-between md:justify-end gap-3 md:gap-4 border-t border-slate-800 mt-6">
                        <a href="{{ route('user.profile.index') }}"
                            class="px-5 py-2.5 rounded text-xs md:text-sm font-bold text-slate-500 hover:text-white hover:bg-slate-800/70 transition uppercase tracking-wide">
                            Hủy bỏ
                        </a>

                        <button
                            class="bg-orange-500 hover:bg-orange-600 text-white px-7 md:px-8 py-2.5 rounded text-xs md:text-sm font-bold
                                   shadow-lg shadow-orange-900/40 transition transform hover:-translate-y-0.5 active:translate-y-0
                                   flex items-center gap-2 uppercase tracking-wide">
                            <i class="fas fa-save text-sm"></i>
                            Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <!-- RIGHT COLUMN: Image & Overlay Info (giống form cấu hình nhân sự nhưng đặt bên phải) -->
        <div class="w-full md:w-5/12 relative bg-black">
            <img src="{{ asset('storage/backgrounds/changePassword.jpg') }}"
                alt="Character"
                class="w-full h-full object-cover opacity-90 filter brightness-90 contrast-125">

            <!-- Overlay Gradient để chữ dễ đọc -->
            <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-90"></div>

            <!-- World Line Info (Góc dưới trái) -->
            <div class="absolute bottom-8 left-8 text-[10px] md:text-xs text-slate-400 leading-relaxed tracking-wider">
                <p class="mb-1">
                    <span class="opacity-50 font-mono">SUBJECT:</span>
                    <span class="text-white font-sans">
                        @php
                        $authUser = Auth::user();
                        @endphp
                        {{ $authUser ? mb_strtoupper($authUser->name, 'UTF-8') : 'USER' }}
                    </span>
                </p>
                <p class="mb-1">
                    <span class="opacity-50 font-mono">TIMELINE:</span>
                    <span class="text-white font-mono">1.048596</span>
                </p>
                <p>
                    <span class="opacity-50 font-mono">STATUS:</span>
                    <span class="text-green-400 font-mono">SECURE</span>
                </p>
            </div>
        </div>

    </div>
</div>
@endsection