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
                    <tr class="hover:bg-slate-700/30 transition group cursor-pointer js-user-row"
                        data-id="{{ str_pad($u->id, 3, '0', STR_PAD_LEFT) }}"
                        data-name="{{ $u->name }}"
                        data-email="{{ $u->email }}"
                        data-role="{{ $u->role }}"
                        data-avatar="{{ $u->avatar_url }}"
                        data-joined="{{ $u->created_at->format('d/m/Y') }}"
                        data-active="{{ $u->is_active ? '1' : '0' }}"
                        data-verified="{{ $u->email_verified_at ? '1' : '0' }}">
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
                                    <div class="font-bold text-white brand-font text-sm truncate group-hover:text-orange-500 transition-colors">
                                        {{ $u->name }}
                                    </div>
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

                        <!-- Actions (đánh dấu no-modal để JS bỏ qua khi click) -->
                        <td class="px-6 py-4 text-right whitespace-nowrap no-modal">
                            <div class="flex items-center justify-end gap-2 opacity-70 group-hover:opacity-100 transition-opacity">
                                <!-- Toggle Active -->
                                @if(Auth::id() !== $u->id)
                                <form action="{{ route('admin.users.toggle-active', $u->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center border transition {{ $u->is_active ? 'border-yellow-500/30 text-yellow-500 hover:bg-yellow-500 hover:text-black' : 'border-green-500/30 text-green-500 hover:bg-green-500 hover:text-white' }}"
                                        title="{{ $u->is_active ? 'Khóa tài khoản' : 'Mở khóa' }}">
                                        <i class="fas {{ $u->is_active ? 'fa-lock' : 'fa-unlock' }}"></i>
                                    </button>
                                </form>
                                @endif

                                <!-- Edit -->
                                <a href="{{ route('admin.users.edit', $u->id) }}"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center bg-slate-700 text-slate-400 hover:bg-blue-600 hover:text-white transition"
                                    title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <!-- Delete -->
                                @if(Auth::id() !== $u->id)
                                <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="inline-block"
                                    onsubmit="return confirm('CẢNH BÁO: Bạn có chắc chắn muốn xóa thành viên {{ $u->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center bg-slate-700 text-slate-400 hover:bg-red-600 hover:text-white transition"
                                        title="Xóa">
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

{{-- MODAL OVERLAY: Xem chi tiết thành viên --}}
<div id="userDetailModal"
    class="fixed inset-0 z-50 hidden items-center justify-center px-4 py-10">

    {{-- Lớp nền mờ phía sau --}}
    <div id="userDetailOverlay" class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm"></div>

    {{-- CARD CHÍNH --}}
    <div class="relative w-full max-w-[950px] mx-auto">

        <div class="bg-slate-900 rounded-xl border border-slate-800 shadow-2xl overflow-hidden relative">

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
                            <img id="modalAvatar"
                                src=""
                                class="w-full h-full object-cover">
                        </div>
                    </div>

                    <!-- NAME + INFO -->
                    <div class="flex flex-col">

                        <!-- NAME -->
                        <h1 id="modalName" class="text-3xl font-bold text-white tracking-tight">
                            <!-- Tên user -->
                        </h1>

                        <!-- USER ID -->
                        <p class="text-slate-400 text-xs font-mono tracking-[0.25em]">
                            USER #<span id="modalUserId"></span>
                        </p>

                        <!-- ROLE + VERIFY BADGE WRAPPER -->
                        <div class="flex flex-col gap-2 mt-3">

                            <!-- BADGE ROW -->
                            <div class="flex items-center gap-3">

                                {{-- ROLE BADGE --}}
                                <span id="badgeRoleAdmin"
                                    class="hidden inline-flex items-center px-3 py-1 rounded-full text-xs font-bold 
                                           bg-red-500/10 text-red-500 border border-red-500/20 shadow-sm">
                                    <i class="fas fa-shield-alt mr-1.5"></i> Admin
                                </span>

                                <span id="badgeRolePoster"
                                    class="hidden inline-flex items-center px-3 py-1 rounded-full text-xs font-bold 
                                           bg-blue-500/10 text-blue-500 border border-blue-500/20 shadow-sm">
                                    <i class="fas fa-feather-alt mr-1.5"></i> Poster
                                </span>

                                <span id="badgeRoleUser"
                                    class="hidden inline-flex items-center px-3 py-1 rounded-full text-xs font-bold 
                                           bg-slate-700 text-slate-300 border border-slate-600 shadow-sm">
                                    <i class="fas fa-user mr-1.5"></i> User
                                </span>

                                {{-- VERIFY STATUS --}}
                                <span id="badgeVerified"
                                    class="hidden inline-flex items-center px-3 py-1 rounded-full text-xs font-bold 
                                           bg-green-500/10 text-green-400 border border-green-500/20">
                                    <i class="fas fa-check-circle mr-1.5"></i> Verified
                                </span>

                                <span id="badgeUnverified"
                                    class="hidden inline-flex items-center px-3 py-1 rounded-full text-xs font-bold 
                                           bg-yellow-500/10 text-yellow-400 border border-yellow-500/20">
                                    <i class="fas fa-exclamation-circle mr-1.5"></i> Unverified
                                </span>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <!-- SEPARATOR -->
            <div class="h-px bg-slate-800"></div>

            <!-- BODY CONTENT -->
            <div class="p-10 grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">

                <!-- Email -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Email</label>
                    <div class="bg-slate-900/60 border border-slate-800 rounded px-4 py-3">
                        <span id="modalEmail" class="text-slate-200 text-sm"></span>
                    </div>
                </div>

                <!-- Role -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Vai trò</label>
                    <div class="bg-slate-900/60 border border-slate-800 rounded px-4 py-3">
                        <span id="modalRoleText" class="text-sky-400 text-sm font-semibold"></span>
                    </div>
                </div>

                <!-- Join Date -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Ngày tham gia</label>
                    <div class="bg-slate-900/60 border border-slate-800 rounded px-4 py-3">
                        <span id="modalJoined" class="text-slate-300 text-sm font-mono"></span>
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Trạng thái</label>
                    <div class="bg-slate-900/60 border border-slate-800 rounded px-4 py-3">
                        <span id="modalStatusLabel" class="text-sm font-mono text-green-400">ACTIVE</span>
                    </div>
                </div>

            </div>

            <!-- SEPARATOR -->
            <div class="h-px bg-slate-800"></div>

            <!-- ACTION BUTTONS (chỉ có nút Đóng cho admin xem nhanh) -->
            <div class="p-6 flex justify-end gap-3">
                <button type="button"
                    class="js-user-modal-close flex items-center gap-2 bg-slate-800 hover:bg-slate-700 text-slate-100 text-xs font-bold py-2.5 px-5 rounded border border-slate-600 transition uppercase tracking-wide">
                    <i class="fas fa-times text-sm"></i>
                    Đóng
                </button>
            </div>

        </div>

    </div>
</div>

{{-- SCRIPT: điều khiển modal --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('userDetailModal');
        const overlay = document.getElementById('userDetailOverlay');
        const rows = document.querySelectorAll('.js-user-row');
        const closeBtns = document.querySelectorAll('.js-user-modal-close');

        const avatarEl = document.getElementById('modalAvatar');
        const nameEl = document.getElementById('modalName');
        const idEl = document.getElementById('modalUserId');
        const emailEl = document.getElementById('modalEmail');
        const roleTextEl = document.getElementById('modalRoleText');
        const joinedEl = document.getElementById('modalJoined');
        const statusLabelEl = document.getElementById('modalStatusLabel');

        const badgeRoleAdmin = document.getElementById('badgeRoleAdmin');
        const badgeRolePoster = document.getElementById('badgeRolePoster');
        const badgeRoleUser = document.getElementById('badgeRoleUser');

        const badgeVerified = document.getElementById('badgeVerified');
        const badgeUnverified = document.getElementById('badgeUnverified');
        const verifyNotice = document.getElementById('verifyNotice'); // không có cũng không sao, đã có if

        function openModal(row) {
            const role = row.dataset.role || 'user';
            const isActive = row.dataset.active === '1';
            const isVerified = row.dataset.verified === '1';

            // Đổ dữ liệu
            avatarEl.src = row.dataset.avatar || '';
            nameEl.textContent = row.dataset.name || '';
            idEl.textContent = row.dataset.id || '';
            emailEl.textContent = row.dataset.email || '';
            roleTextEl.textContent = role.toUpperCase();
            joinedEl.textContent = row.dataset.joined || '';

            // Trạng thái
            statusLabelEl.textContent = isActive ? 'ACTIVE' : 'INACTIVE';
            statusLabelEl.classList.toggle('text-green-400', isActive);
            statusLabelEl.classList.toggle('text-red-400', !isActive);

            // Role badges
            badgeRoleAdmin.classList.add('hidden');
            badgeRolePoster.classList.add('hidden');
            badgeRoleUser.classList.add('hidden');

            if (role === 'admin') {
                badgeRoleAdmin.classList.remove('hidden');
            } else if (role === 'poster') {
                badgeRolePoster.classList.remove('hidden');
            } else {
                badgeRoleUser.classList.remove('hidden');
            }

            // Xác thực
            badgeVerified.classList.toggle('hidden', !isVerified);
            badgeUnverified.classList.toggle('hidden', isVerified);

            if (verifyNotice) {
                verifyNotice.classList.toggle('hidden', isVerified);
            }

            // Mở modal
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // 👉 Click cả dòng <tr> để mở modal, trừ cột .no-modal (Hành động)
        rows.forEach(row => {
            row.addEventListener('click', (e) => {
                if (e.target.closest('.no-modal')) return; // bỏ qua click trong cột nút
                openModal(row);
            });
        });

        // Nút Đóng
        closeBtns.forEach(btn => {
            btn.addEventListener('click', closeModal);
        });

        // Click nền mờ để đóng
        if (overlay) {
            overlay.addEventListener('click', closeModal);
        }

        // ESC để đóng
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        });
    });
</script>
@endsection