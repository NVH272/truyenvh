{{-- FILE: resources/views/admin/live_chat/partials/user_item.blade.php --}}
<div class="conversation-item {{ (isset($user) && $u->id == $user->id) ? 'active' : '' }}"
    onclick="loadChat('{{ $u->id }}', this)">

    <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" class="conversation-avatar">

    <div class="conversation-info">
        <div class="conversation-name">
            <span class="truncate max-w-[120px]">{{ $u->name }}</span>

            @if($role === 'admin')
            <span class="role-badge role-admin">AD</span>
            @elseif($role === 'poster')
            <span class="role-badge role-poster">POSTER</span>
            @endif
        </div>
        <div class="conversation-preview font-term">{{ $u->email }}</div>
    </div>

    @if($u->unread_count > 0)
    <span class="unread-badge">{{ $u->unread_count }}</span>
    @endif
</div>