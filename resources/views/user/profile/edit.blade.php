@extends('layouts.app')

@section('title', 'Cấu hình nhân sự')

@section('content')
<div class="min-h-[calc(100vh-140px)] flex items-center justify-center px-4 py-10">

    <!-- Main Container Card -->
    <div class="w-full max-w-[1100px] bg-slate-900 rounded-lg shadow-2xl overflow-hidden flex flex-col md:flex-row min-h-[520px]">

        <!-- LEFT COLUMN: Image & Overlay Info -->
        <div class="w-full md:w-5/12 relative bg-black">
            <img src="{{ asset('storage/backgrounds/editInfo.jpg') }}"
                alt="Character"
                class="w-full h-full object-cover opacity-90 filter brightness-90 contrast-125">

            <!-- Overlay Gradient để chữ dễ đọc -->
            <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-90"></div>

            <!-- World Line Info (Góc dưới trái) -->
            <div class="absolute bottom-8 left-8 text-[10px] md:text-xs text-slate-400 leading-relaxed tracking-wider">
                <p class="mb-1">
                    <span class="opacity-50 font-mono">SUBJECT:</span>
                    <span class="text-white font-sans">
                        {{ mb_strtoupper($user->name, 'UTF-8') }}
                    </span>
                </p>
                <p class="mb-1">
                    <span class="opacity-50 font-mono">TIMELINE:</span>
                    <span class="text-white font-mono">1.048596</span>
                </p>
                <p>
                    <span class="opacity-50 font-mono">STATUS:</span>

                    @if($user->is_active == 1)
                    <span class="text-green-400 font-mono">ACTIVE</span>
                    @else
                    <span class="text-red-400 font-mono">INACTIVE</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- RIGHT COLUMN: Form Config -->
        <div class="w-full md:w-7/12 px-8 py-8 md:px-12 md:py-10 relative flex flex-col bg-slate-950 text-slate-100">

            <!-- Decorative Background Gear -->
            <div class="absolute bottom-0 right-0 opacity-5 pointer-events-none">
                <i class="fas fa-cog text-9xl"></i>
            </div>

            <!-- HEADER -->
            <div class="flex justify-between items-start mb-8 border-b border-white/5 pb-4 relative z-10">
                <h1 class="text-xl md:text-2xl font-bold uppercase tracking-wide flex items-center gap-2">
                    <span class="text-orange-500 font-mono">>_</span> CẤU HÌNH NHÂN SỰ
                </h1>
                <!-- Badge FG204 -->
                <div class="border border-orange-500/40 text-orange-500 text-[10px] font-mono px-2 py-1 rounded bg-orange-500/5 tracking-[0.25em]">
                    FG204 <span class="animate-pulse">●</span>
                </div>
            </div>

            <!-- Form Start -->
            <form action="{{ route('user.profile.updateInfo') }}"
                method="POST"
                enctype="multipart/form-data"
                class="flex-grow flex flex-col gap-6 relative z-10">
                @csrf
                @method('PUT')

                <!-- USER INFO SECTION -->
                <div class="flex items-center gap-5 mb-2">
                    <!-- Avatar Circle -->
                    <div class="relative group cursor-pointer">
                        <div class="w-20 h-20 rounded-full bg-slate-800 flex items-center justify-center text-white text-2xl font-bold shadow-lg shadow-cyan-500/20 overflow-hidden border border-slate-600 group-hover:border-orange-500 transition-colors">
                            @if($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" id="avatar-preview" class="w-full h-full object-cover">
                            @else
                            <span>
                                {{ strtoupper(mb_substr($user->name,0,1)) }}
                            </span>
                            @endif
                        </div>

                        <!-- Input file ẩn -->
                        <input type="file" name="avatar" id="avatar" class="hidden" accept="image/*" onchange="previewAvatar(this)">

                        <!-- Overlay click -->
                        <label for="avatar"
                            class="absolute inset-0 rounded-full flex items-center justify-center bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fas fa-camera text-white text-sm"></i>
                        </label>

                        <!-- Status Dot -->
                        <div class="absolute bottom-1 right-1 w-4 h-4 bg-green-500 border-2 border-slate-900 rounded-full"></div>
                    </div>

                    <!-- Name & Upload Button -->
                    <div>
                        <h2 class="text-2xl font-bold text-white tracking-tight">{{ $user->name }}</h2>
                        <p class="text-xs text-slate-400 uppercase font-mono tracking-[0.25em] mb-3">
                            LABMEMBER #{{ str_pad($user->id, 3, '0', STR_PAD_LEFT) }}
                        </p>

                        <label for="avatar"
                            class="inline-block text-[10px] font-bold bg-slate-900 border border-slate-700 hover:border-slate-500 text-slate-300 px-3 py-1.5 rounded transition-colors uppercase tracking-wide cursor-pointer">
                            Upload Avatar Mới
                        </label>
                        @error('avatar')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- INPUT: CODENAME -->
                <div class="group">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2 group-focus-within:text-orange-500 transition-colors">
                        Họ và Tên
                    </label>
                    <div class="relative">
                        <input type="text"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            class="w-full bg-slate-900 border border-slate-700 text-white text-sm rounded px-4 py-3 outline-none transition-all placeholder-slate-500 focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                        <i class="fas fa-pencil absolute right-4 top-1/2 -translate-y-1/2 text-slate-600 text-sm"></i>
                    </div>
                    @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- INPUT: D-MAIL ADDRESS -->
                <div class="group">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2 group-focus-within:text-orange-500 transition-colors">
                        Email
                    </label>
                    <div class="relative">
                        <input type="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            class="w-full bg-slate-900 border border-slate-700 text-white text-sm rounded px-4 py-3 outline-none transition-all placeholder-slate-500 focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                        <i class="fas fa-envelope absolute right-4 top-1/2 -translate-y-1/2 text-slate-600 text-sm"></i>
                    </div>
                    @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- GRID: ROLE & DATE -->
                <div class="grid grid-cols-2 gap-5">

                    <!-- Role (Readonly) -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">
                            Vai trò
                        </label>

                        <div class="relative bg-slate-900/60 rounded px-4 py-3">
                            <span class="text-sky-400 font-semibold text-sm">
                                @if($user->role === 'admin')
                                Administrator
                                @elseif($user->role === 'poster')
                                Poster
                                @else
                                Member
                                @endif
                            </span>

                            <!-- LOCK ICON -->
                            <i class="fas fa-lock text-slate-500 absolute right-3 top-1/2 -translate-y-1/2 text-xs opacity-70"></i>
                        </div>
                    </div>

                    <!-- Join Date (Readonly) -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">
                            Ngày tham gia
                        </label>

                        <div class="relative bg-slate-900/60 rounded px-4 py-3">
                            <span class="text-slate-300 font-mono text-sm">
                                {{ $user->created_at->format('d/m/Y') }}
                            </span>

                            <!-- LOCK ICON -->
                            <i class="fas fa-lock text-slate-500 absolute right-3 top-1/2 -translate-y-1/2 text-xs opacity-70"></i>
                        </div>
                    </div>

                </div>

                <!-- FOOTER ACTIONS -->
                <div class="flex justify-between items-center mt-6 pt-4 border-t border-slate-800">
                    <a href="{{ route('user.profile.index') }}"
                        class="text-xs font-bold text-slate-500 hover:text-white transition-colors flex items-center gap-1 uppercase tracking-[0.18em]">
                        <i class="fas fa-chevron-left text-[10px]"></i>
                        Quay lại
                    </a>

                    <button type="submit"
                        class="bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold py-3 px-6 rounded shadow-lg shadow-orange-900/20 flex items-center gap-2 transition-all transform active:scale-95 uppercase tracking-wide">
                        <i class="fas fa-save text-sm"></i>
                        Lưu thay đổi
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('avatar-preview');
                if (img) {
                    img.src = e.target.result;
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection