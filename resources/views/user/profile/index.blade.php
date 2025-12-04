@extends('layouts.app')

@section('title', 'Trang cá nhân')

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- Flash message --}}
    @if(session('success'))
    <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 text-sm">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white/90 rounded-xl shadow-lg border border-gray-200 p-6 flex flex-col md:flex-row gap-6">
        {{-- Avatar --}}
        <div class="flex flex-col items-center md:w-1/3">
            <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-blue-500/70 shadow-md mb-3">
                <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-full h-full object-cover">
            </div>
            <span class="text-xs text-gray-500 italic">Avatar hiện tại</span>
        </div>

        {{-- Info --}}
        <div class="flex-1 space-y-3">
            <h2 class="text-xl font-bold text-gray-800 mb-2">Thông tin tài khoản</h2>

            <div class="space-y-2 text-sm">
                <p><span class="font-semibold text-gray-600">Họ tên:</span> {{ $user->name }}</p>
                <p><span class="font-semibold text-gray-600">Email:</span> {{ $user->email }}</p>
                <p>
                    <span class="font-semibold text-gray-600">Vai trò:</span>
                    <span class="uppercase text-xs font-bold px-2 py-1 rounded-full 
                        {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ $user->role }}
                    </span>
                </p>
                @if(isset($user->is_active))
                <p>
                    <span class="font-semibold text-gray-600">Trạng thái:</span>
                    @if($user->is_active)
                    <span class="text-green-600 font-semibold">Đang hoạt động</span>
                    @else
                    <span class="text-red-600 font-semibold">Bị khóa</span>
                    @endif
                </p>
                @endif
            </div>

            <div class="flex flex-wrap gap-3 mt-4">
                <a href="{{ route('user.profile.editInfo') }}"
                    class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition">
                    <i class="fas fa-user-edit mr-2"></i> Thay đổi thông tin
                </a>

                <a href="{{ route('user.profile.editPassword') }}"
                    class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-800 text-white text-sm font-semibold hover:bg-black transition">
                    <i class="fas fa-key mr-2"></i> Đổi mật khẩu
                </a>
            </div>
        </div>
    </div>
</div>
@endsection