@extends('layouts.admin')

@section('title', 'Danh sách Thành viên')
@section('header', 'Quản lý Thành viên')

@section('content')
<div class="space-y-6">
    <!-- Toolbar -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-slate-800 p-4 rounded-xl border border-slate-700 shadow-sm">

        <!-- Filter Form -->
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <!-- Search Input -->
            <div class="relative w-full sm:w-72 group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-slate-500 group-focus-within:text-orange-500 transition-colors"></i>
                </div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Tìm tên hoặc email..." class="admin-input w-full pl-10 pr-4 py-2.5 rounded-lg text-sm focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 bg-slate-900/50 border-slate-700 text-white placeholder-slate-500 transition-all">
            </div>

            <!-- Role Filter -->
            <div class="relative w-full sm:w-48">
                <select name="role" onchange="this.form.submit()" class="admin-input w-full px-4 py-2.5 rounded-lg text-sm bg-slate-900/50 border-slate-700 text-white focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 cursor-pointer appearance-none">
                    <option value="">-- Tất cả vai trò --</option>
                    <option value="admin" {{ $role == 'admin' ? 'selected' : '' }}>Admin (Quản trị)</option>
                    <option value="poster" {{ $role == 'poster' ? 'selected' : '' }}>Poster (Người đăng)</option>
                    <option value="user" {{ $role == 'user' ? 'selected' : '' }}>User (Thành viên)</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none text-slate-500">
                    <i class="fas fa-chevron-down text-xs"></i>
                </div>
            </div>
        </form>

        <!-- Add Button -->
        <a href="{{ route('admin.users.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-lg shadow-orange-900/20 transition flex items-center gap-2 transform active:scale-95 whitespace-nowrap">
            <i class="fas fa-user-plus"></i>
            <span>Thêm Thành viên</span>
        </a>
    </div>

    <!-- Data Table Card -->
    <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-900/70 border-b border-slate-700 text-slate-400 uppercase tracking-wider brand-font text-xs">
                        <th class="px-6 py-4 font-bold text-center w-20">ID</th>
                        <th class="px-6 py-4 font-bold text-left">Thành viên</th>
                        <th class="px-6 py-4 font-bold text-center">Vai trò</th>
                        <th class="px-6 py-4 font-bold text-center">Trạng thái</th>
                        <th class="px-6 py-4 font-bold text-left whitespace-nowrap">Ngày tham gia</th>
                        <th class="px-6 py-4 font-bold text-right">Hành động</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-700/50">
                    @forelse ($users as $u)
                    <tr class="hover:bg-slate-700/30 transition group">
                        <!-- ID -->
                        <td class="px-6 py-4 text-center font-mono-tech text-slate-500 whitespace-nowrap">
                            #{{ str_pad($u->id, 3, '0', STR_PAD_LEFT) }}
                        </td>

                        <!-- User Info -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <img class="w-10 h-10 rounded-full border-2 border-slate-600 shadow-sm flex-shrink-0 object-cover group-hover:border-orange-500 transition-colors"
                                    src="{{ $u->avatar_url }}"
                                    alt="{{ $u->name }}">
                                <div class="min-w-0">
                                    <div class="font-bold text-white brand-font text-sm truncate group-hover:text-orange-500 transition-colors">{{ $u->name }}</div>
                                    <div class="text-xs text-slate-500 font-mono-tech truncate">{{ $u->email }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- Role Badge -->
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            @if($u->role === 'admin')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-500/10 text-red-500 border border-red-500/20 shadow-sm">
                                <i class="fas fa-shield-alt mr-1.5"></i> Admin
                            </span>
                            @elseif($u->role === 'poster')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-500/10 text-blue-500 border border-blue-500/20">
                                <i class="fas fa-feather-alt mr-1.5"></i> Poster
                            </span>
                            @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-700 text-slate-400 border border-slate-600">
                                <i class="fas fa-user mr-1.5"></i> User
                            </span>
                            @endif
                        </td>

                        <!-- Status Badge -->
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            @if($u->is_active)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-500/10 text-green-500 border border-green-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5 animate-pulse"></span> Hoạt động
                            </span>
                            @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-700 text-gray-400 border border-gray-600 opacity-75">
                                <i class="fas fa-lock mr-1.5 text-[10px]"></i> Đã khóa
                            </span>
                            @endif
                        </td>

                        <!-- Created At -->
                        <td class="px-6 py-4 text-left whitespace-nowrap">
                            <div class="text-slate-300 text-sm font-mono-tech">{{ $u->created_at->format('d/m/Y') }}</div>
                            <div class="text-xs text-slate-600">{{ $u->created_at->format('H:i') }}</div>
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2 opacity-70 group-hover:opacity-100 transition-opacity">
                                <!-- Toggle Active -->
                                @if(Auth::id() !== $u->id)
                                <form action="{{ route('admin.users.toggle-active', $u->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center border transition {{ $u->is_active ? 'border-yellow-500/30 text-yellow-500 hover:bg-yellow-500 hover:text-black' : 'border-green-500/30 text-green-500 hover:bg-green-500 hover:text-white' }}" title="{{ $u->is_active ? 'Khóa tài khoản' : 'Mở khóa' }}">
                                        <i class="fas {{ $u->is_active ? 'fa-lock' : 'fa-unlock' }}"></i>
                                    </button>
                                </form>
                                @endif

                                <!-- View Details -->
                                <a href="#" class="w-8 h-8 rounded-lg flex items-center justify-center bg-slate-700 text-slate-400 hover:bg-teal-500 hover:text-white transition" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <!-- Edit -->
                                <a href="{{ route('admin.users.edit', $u->id) }}" class="w-8 h-8 rounded-lg flex items-center justify-center bg-slate-700 text-slate-400 hover:bg-blue-600 hover:text-white transition" title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <!-- Delete -->
                                @if(Auth::id() !== $u->id)
                                <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="inline-block" onsubmit="return confirm('CẢNH BÁO: Bạn có chắc chắn muốn xóa thành viên {{ $u->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center bg-slate-700 text-slate-400 hover:bg-red-600 hover:text-white transition" title="Xóa">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-users-slash text-4xl mb-3 text-slate-600"></i>
                                <p>Không tìm thấy thành viên nào phù hợp.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-700 bg-slate-800 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-slate-400">

            {{-- Hiển thị thông tin --}}
            <span class="order-2 sm:order-1">
                Hiển thị
                <strong>{{ $users->firstItem() }}</strong> -
                <strong>{{ $users->lastItem() }}</strong>
                trong tổng số
                <strong>{{ $users->total() }}</strong> kết quả
            </span>

            {{-- Nút chuyển trang --}}
            <div class="flex gap-1 order-1 sm:order-2">

                {{-- Previous --}}
                <a href="{{ $users->previousPageUrl() }}"
                    class="px-3 py-1.5 rounded-md border border-slate-600 hover:bg-slate-700 hover:text-white transition 
            {{ $users->onFirstPage() ? 'opacity-50 pointer-events-none' : '' }}">
                    <i class="fas fa-chevron-left"></i>
                </a>

                {{-- Numbered Pages --}}
                @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                <a href="{{ $url }}"
                    class="px-3 py-1.5 rounded-md border border-slate-600 hover:bg-slate-700 hover:text-white transition
                {{ $page == $users->currentPage() ? 'bg-orange-600 text-white font-bold border-none' : '' }}">
                    {{ $page }}
                </a>
                @endforeach

                {{-- Next --}}
                <a href="{{ $users->nextPageUrl() }}"
                    class="px-3 py-1.5 rounded-md border border-slate-600 hover:bg-slate-700 hover:text-white transition
            {{ !$users->hasMorePages() ? 'opacity-50 pointer-events-none' : '' }}">
                    <i class="fas fa-chevron-right"></i>
                </a>

            </div>

        </div>
    </div>
</div>
@endsection