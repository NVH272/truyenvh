@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân')

@section('content')

<div class="min-h-[calc(100vh-140px)] flex items-center justify-center px-4 py-10">

    <div class="w-full max-w-[950px] mx-auto">

        <!-- MAIN CARD -->
        <div class="bg-slate-900 rounded-xl border border-slate-800 shadow-xl overflow-hidden">

            <!-- TOP SECTION (Avatar + Name + Role) -->
            <div class="px-10 pt-10 pb-6 relative">

                <!-- Background icon -->
                <div class="absolute right-6 top-6 opacity-[0.05] pointer-events-none">
                    <i class="fas fa-id-badge text-[120px] text-white"></i>
                </div>

                <!-- AVATAR + NAME + ROLE + VERIFY STATUS -->
                <div class="flex items-center gap-6 relative z-10">

                    <!-- AVATAR -->
                    <div class="relative">
                        <div class="w-24 h-24 rounded-full bg-slate-800 border border-slate-600 shadow-lg overflow-hidden flex items-center justify-center">
                            <img src="{{ $user->avatar_url ?? asset('images/default-avatar.png') }}"
                                class="w-full h-full object-cover">
                        </div>
                    </div>

                    <!-- NAME + INFO -->
                    <div class="flex flex-col">

                        <!-- NAME -->
                        <h1 class="text-3xl font-bold text-white tracking-tight">
                            {{ $user->name }}
                        </h1>

                        <!-- USER ID -->
                        <p class="text-slate-400 text-xs font-mono tracking-[0.25em]">
                            USER #{{ str_pad($user->id, 3, '0', STR_PAD_LEFT) }}
                        </p>

                        <!-- ROLE BADGE -->
                        <div class="flex items-center gap-3 mt-3">

                            {{-- ROLE --}}
                            @if($user->role === 'admin')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold 
                             bg-red-500/10 text-red-500 border border-red-500/20 shadow-sm">
                                <i class="fas fa-shield-alt mr-1.5"></i> Admin
                            </span>

                            @elseif($user->role === 'poster')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold 
                             bg-blue-500/10 text-blue-500 border border-blue-500/20 shadow-sm">
                                <i class="fas fa-feather-alt mr-1.5"></i> Poster
                            </span>

                            @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold 
                             bg-slate-700 text-slate-300 border border-slate-600 shadow-sm">
                                <i class="fas fa-user mr-1.5"></i> User
                            </span>
                            @endif

                            {{-- VERIFY STATUS --}}
                            @if($user->email_verified_at)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold 
                             bg-green-500/10 text-green-400 border border-green-500/20">
                                <i class="fas fa-check-circle mr-1.5"></i> Verified
                            </span>
                            @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold 
                             bg-yellow-500/10 text-yellow-400 border border-yellow-500/20">
                                <i class="fas fa-exclamation-circle mr-1.5"></i> Unverified
                            </span>
                            @endif

                            {{-- THÔNG BÁO THÊM KHI CHƯA XÁC THỰC --}}
                            @if(!$user->email_verified_at)
                            <p class="mt-2 text-xs text-yellow-400 font-medium">
                                Email chưa được xác thực. Hãy kiểm tra hộp thư để xác nhận và tiếp tục sử dụng mọi tính năng.
                            </p>

                            @endif

                        </div>

                    </div>

                </div>

            </div>

            <!-- SEPARATOR -->
            <div class="h-px bg-slate-800"></div>

            <!-- BODY CONTENT -->
            <div class="p-10 grid grid-cols-1 md:grid-cols-2 gap-8">

                <!-- Email -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Email</label>
                    <div class="bg-slate-900/60 border border-slate-800 rounded px-4 py-3">
                        <span class="text-slate-200 text-sm">{{ $user->email }}</span>
                    </div>
                </div>

                <!-- Role -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Vai trò</label>
                    <div class="bg-slate-900/60 border border-slate-800 rounded px-4 py-3">
                        <span class="text-sky-400 text-sm font-semibold">{{ strtoupper($user->role) }}</span>
                    </div>
                </div>

                <!-- Join Date -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Ngày tham gia</label>
                    <div class="bg-slate-900/60 border border-slate-800 rounded px-4 py-3">
                        <span class="text-slate-300 text-sm font-mono">{{ $user->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Trạng thái</label>
                    <div class="bg-slate-900/60 border border-slate-800 rounded px-4 py-3">
                        @if($user->is_active)
                        <span class="text-green-400 text-sm font-mono">ACTIVE</span>
                        @else
                        <span class="text-red-400 text-sm font-mono">INACTIVE</span>
                        @endif
                    </div>
                </div>

            </div>

            <!-- SEPARATOR -->
            <div class="h-px bg-slate-800"></div>

            <!-- ACTION BUTTONS -->
            <div class="p-10 flex flex-wrap gap-4">

                <a href="{{ route('user.profile.editInfo') }}"
                    class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold py-3 px-6 rounded shadow-md transition uppercase tracking-wide">
                    <i class="fas fa-user-edit text-sm"></i>
                    Cập nhật thông tin
                </a>

                <a href="{{ route('user.profile.editPassword') }}"
                    class="flex items-center gap-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold py-3 px-6 rounded border border-slate-700 transition uppercase tracking-wide">
                    <i class="fas fa-key text-sm"></i>
                    Đổi mật khẩu
                </a>

            </div>

        </div>

    </div>

</div>

@endsection