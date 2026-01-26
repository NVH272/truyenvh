@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>📩 Chat với Khách hàng</h3>

    <ul class="list-group">
        @foreach($users as $user)
        <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ $user->name }} ({{ $user->email }})
            <a href="{{ route('admin.messages.chat', $user->id) }}" class="btn btn-sm btn-outline-primary">Chat</a>
        </li>
        @endforeach
    </ul>
</div>
@endsection