<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Lấy danh sách thông báo & đếm số lượng chưa đọc
    public function index(Request $request)
    {
        // Nếu không phải request AJAX / JSON (ví dụ gõ URL trực tiếp sau khi đăng nhập)
        // thì không trả JSON thô mà đưa người dùng về trang chủ.
        if (!$request->expectsJson() && !$request->ajax()) {
            return redirect()->route('home');
        }

        $user = Auth::user();

        // Lấy 10 thông báo mới nhất
        $notifications = $user->notifications()->take(10)->get();

        // Đếm số thông báo chưa đọc
        $unreadCount = $user->unreadNotifications->count();

        // Render view partial trả về HTML
        $html = view('user.comics.partials.notification_list', compact('notifications'))->render();

        return response()->json([
            'html' => $html,
            'unread_count' => $unreadCount
        ]);
    }

    // Đánh dấu tất cả là đã đọc (nút "Đánh dấu đã đọc" trong dropdown)
    public function markAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }

    // Đánh dấu 1 thông báo là đã đọc (từ menu ba chấm)
    public function markOne($id, Request $request)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back();
    }

    // Xoá 1 thông báo (từ menu ba chấm)
    public function destroy($id, Request $request)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back();
    }

    // Đánh dấu 1 cái là đã đọc và chuyển hướng (khi click vào 1 thông báo)
    public function readAndRedirect($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        // Lấy link từ data đã lưu
        return redirect($notification->data['url']);
    }
}
