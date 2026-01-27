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

    // Trang chat index cho user
    public function userIndex()
    {
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
        })->orderBy('created_at', 'asc')->get();

        return view('user.live_chat.index', compact('messages', 'admin'));
    }

    // Lấy tin nhắn cho chatbox trong layout (dùng cho cả user và admin)
    public function getMessages(Request $request)
    {
        $user = Auth::user();
        $receiverId = $request->input('receiver_id');

        if (!$receiverId) {
            // Nếu là user/poster: lấy admin đầu tiên
            if ($user->role === 'user' || $user->role === 'poster') {
                $receiver = User::where('role', 'admin')->first();
                if (!$receiver) {
                    return response()->json(['error' => 'Không tìm thấy admin'], 404);
                }
                $receiverId = $receiver->id;
            } else {
                return response()->json(['error' => 'Vui lòng chọn người nhận'], 400);
            }
        } else {
            $receiver = User::findOrFail($receiverId);
        }

        // Kiểm tra quyền chat
        if ($user->role === 'user' || $user->role === 'poster') {
            // User/poster chỉ được chat với admin
            if ($receiver->role !== 'admin') {
                return response()->json(['error' => 'Bạn chỉ có thể chat với admin'], 403);
            }
            // Kiểm tra email đã xác thực
            if (!$user->hasVerifiedEmail()) {
                return response()->json(['error' => 'Vui lòng xác thực email để sử dụng chat'], 403);
            }
        } elseif ($user->role === 'admin') {
            // Admin có thể chat với admin khác hoặc user/poster đã xác thực
            if ($receiver->id === $user->id) {
                return response()->json(['error' => 'Bạn không thể chat với chính mình'], 403);
            }
            // Nếu chat với user/poster, họ phải đã xác thực email
            if (($receiver->role === 'user' || $receiver->role === 'poster') && !$receiver->hasVerifiedEmail()) {
                return response()->json(['error' => 'Người dùng này chưa xác thực email'], 403);
            }
        }

        // Đánh dấu tin nhắn đã đọc
        $this->markAsRead($receiverId, $user->id);

        // Lấy tin nhắn
        $messages = Message::where(function ($q) use ($user, $receiverId) {
            $q->where('sender_id', $user->id)->where('receiver_id', $receiverId);
        })->orWhere(function ($q) use ($user, $receiverId) {
            $q->where('sender_id', $receiverId)->where('receiver_id', $user->id);
        })->orderBy('created_at', 'asc')->get();

        $receiver = User::find($receiverId);

        return view('user.live_chat.chat', compact('messages', 'receiver'))->render();
    }

    // Gửi tin nhắn
    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message'     => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $receiver = User::findOrFail($request->receiver_id);

        // Kiểm tra quyền chat
        if ($user->role === 'user' || $user->role === 'poster') {
            // User/poster chỉ được chat với admin
            if ($receiver->role !== 'admin') {
                return response()->json(['error' => 'Bạn chỉ có thể chat với admin'], 403);
            }
            // Kiểm tra email đã xác thực
            if (!$user->hasVerifiedEmail()) {
                return response()->json(['error' => 'Vui lòng xác thực email để sử dụng chat'], 403);
            }
        } elseif ($user->role === 'admin') {
            // Admin có thể chat với admin khác hoặc user/poster đã xác thực
            if ($receiver->id === $user->id) {
                return response()->json(['error' => 'Bạn không thể chat với chính mình'], 403);
            }
            // Nếu chat với user/poster, họ phải đã xác thực email
            if (($receiver->role === 'user' || $receiver->role === 'poster') && !$receiver->hasVerifiedEmail()) {
                return response()->json(['error' => 'Người dùng này chưa xác thực email'], 403);
            }
        }

        $message = Message::create([
            'sender_id'   => $user->id,
            'receiver_id' => $request->receiver_id,
            'message'     => $request->message,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message->load('sender')
            ]);
        }

        return back();
    }

    // Lấy danh sách người có thể chat (cho admin)
    public function getChatList(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Admin có thể chat với: user/poster đã xác thực email, và admin khác
            $users = User::where('id', '!=', $user->id)
                ->where(function ($q) {
                    $q->where('role', 'admin')
                        ->orWhere(function ($q2) {
                            $q2->whereIn('role', ['user', 'poster'])
                                ->whereNotNull('email_verified_at');
                        });
                })
                ->where(function ($q) use ($user) {
                    $q->whereHas('sentMessages', function ($q2) use ($user) {
                        $q2->where('receiver_id', $user->id);
                    })
                    ->orWhereHas('receivedMessages', function ($q2) use ($user) {
                        $q2->where('sender_id', $user->id);
                    });
                })
                ->withCount(['sentMessages as unread_count' => function ($q) use ($user) {
                    $q->where('receiver_id', $user->id)->whereNull('read_at');
                }])
                ->orderByDesc('unread_count')
                ->get();
        } else {
            // User/poster: chỉ lấy admin
            $users = User::where('role', 'admin')->get();
        }

        return response()->json(['users' => $users]);
    }

    // --- Phần Admin (trang riêng) ---

    public function adminIndex()
    {
        // Lấy user và poster đã xác thực email đã từng nhắn tin
        $users = User::whereIn('role', ['user', 'poster'])
            ->whereNotNull('email_verified_at')
            ->whereHas('sentMessages', function ($q) {
                $q->where('receiver_id', Auth::id());
            })
            ->withCount(['sentMessages as unread_count' => function ($q) {
                $q->where('receiver_id', Auth::id())->whereNull('read_at');
            }])
            ->orderByDesc('unread_count')
            ->get();

        // Lấy admin khác (không phải bản thân)
        $admins = User::where('role', 'admin')
            ->where('id', '!=', Auth::id())
            ->whereHas('sentMessages', function ($q) {
                $q->where('receiver_id', Auth::id());
            })
            ->orWhereHas('receivedMessages', function ($q) {
                $q->where('sender_id', Auth::id());
            })
            ->withCount(['sentMessages as unread_count' => function ($q) {
                $q->where('receiver_id', Auth::id())->whereNull('read_at');
            }])
            ->orderByDesc('unread_count')
            ->get();

        return view('admin.live_chat.index', compact('users', 'admins'));
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

        // Lấy danh sách users và admins cho sidebar
        $users = User::whereIn('role', ['user', 'poster'])
            ->whereNotNull('email_verified_at')
            ->where(function ($q) {
                $q->whereHas('sentMessages', function ($q2) {
                    $q2->where('receiver_id', Auth::id());
                })
                ->orWhereHas('receivedMessages', function ($q2) {
                    $q2->where('sender_id', Auth::id());
                });
            })
            ->get();

        $admins = User::where('role', 'admin')
            ->where('id', '!=', Auth::id())
            ->where(function ($q) {
                $q->whereHas('sentMessages', function ($q2) {
                    $q2->where('receiver_id', Auth::id());
                })
                ->orWhereHas('receivedMessages', function ($q2) {
                    $q2->where('sender_id', Auth::id());
                });
            })
            ->get();

        if ($request->ajax()) {
            return view('admin.live_chat.chat', compact('messages', 'user', 'users', 'admins'))->render();
        }

        return view('admin.live_chat.chat', compact('user', 'messages', 'users', 'admins'));
    }
}
