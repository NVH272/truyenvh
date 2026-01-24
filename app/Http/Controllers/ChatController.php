<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // Đánh dấu đã đọc tin nhắn
    private function markAsRead($sender_id, $receiver_id)
    {
        Message::where('sender_id', $sender_id)
            ->where('receiver_id', $receiver_id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function index(Request $request)
    {
        // Lấy admin để chat (Tạm thời lấy người đầu tiên, nên nâng cấp logic này sau)
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            return back()->with('error', 'Hệ thống chưa có nhân viên hỗ trợ.');
        }

        // Đánh dấu tin nhắn từ admin gửi cho user này là đã đọc
        $this->markAsRead($admin->id, Auth::id());

        $messages = Message::where(function ($q) use ($admin) {
            $q->where('sender_id', Auth::id())->where('receiver_id', $admin->id);
        })->orWhere(function ($q) use ($admin) {
            $q->where('sender_id', $admin->id)->where('receiver_id', Auth::id());
        })->orderBy('created_at', 'asc')->get(); // Sửa thành ASC để tin cũ hiện trên, tin mới dưới

        if ($request->ajax()) {
            // Trả về view partial thay vì JSON hay String cứng
            return view('chat.partials.messages', compact('messages', 'admin'))->render();
        }

        return view('chat.customer', compact('messages', 'admin'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message'     => 'required|string|max:1000',
        ]);

        // TODO: Kiểm tra xem receiver_id có phải là admin không (để chặn user chat với user)

        $message = Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message'     => $request->message,
        ]);

        if ($request->ajax()) {
            // Trả về view partial của 1 tin nhắn mới để append vào khung chat
            return response()->json([
                'success' => true,
                'html' => view('chat.partials.single_message', compact('message'))->render()
            ]);
        }

        return back();
    }

    // --- Phần Admin ---

    public function adminIndex()
    {
        // Chỉ lấy những user đã từng nhắn tin
        $users = User::where('role', 'user')
            ->whereHas('sentMessages') // Cần định nghĩa relationship sentMessages trong User Model
            ->withCount(['sentMessages as unread_count' => function ($q) {
                $q->where('receiver_id', Auth::id())->whereNull('read_at');
            }])
            ->orderByDesc('unread_count') // Ưu tiên user có tin nhắn chưa đọc
            ->get();

        return view('chat.admin', compact('users'));
    }

    public function adminChat(User $user, Request $request)
    {
        // Đánh dấu tin nhắn user gửi đến admin là đã đọc
        $this->markAsRead($user->id, Auth::id());

        $messages = Message::where(function ($q) use ($user) {
            $q->where('sender_id', Auth::id())->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($user) {
            $q->where('sender_id', $user->id)->where('receiver_id', Auth::id());
        })->orderBy('created_at', 'asc')->get();

        if ($request->ajax()) {
            // SỬ DỤNG VIEW THAY VÌ NỐI CHUỖI HTML
            return view('chat.partials.admin_messages_list', compact('messages', 'user'))->render();
        }

        return view('chat.admin_chat', compact('user', 'messages'));
    }

    // adminSend tương tự send...
}
