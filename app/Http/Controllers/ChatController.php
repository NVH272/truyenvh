<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\Comic;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    // ============================================
    // HELPER: LOGIC TRUY VẤN CHUNG (CORE)
    // ============================================

    /**
     * Hàm này trả về 3 danh sách riêng biệt: Admins, Posters, Users
     * Dùng chung cho adminIndex, adminChat và API
     */
    private function getSharedChatLists($authId)
{
    // 1. LẤY TOÀN BỘ ADMIN (Trừ bản thân)
    // Bỏ điều kiện whereHas sentMessages/receivedMessages
    $admins = User::where('role', 'admin')
        ->where('id', '!=', $authId)
        ->withCount(['sentMessages as unread_count' => function ($q) use ($authId) {
            $q->where('receiver_id', $authId)->whereNull('read_at');
        }])
        ->orderByDesc('unread_count') // Vẫn ưu tiên người có tin chưa đọc lên đầu
        ->orderBy('name', 'asc')      // Sau đó xếp theo tên A-Z
        ->get();

    // 2. LẤY TOÀN BỘ USER & POSTER (Đã xác thực email)
    // Bỏ điều kiện whereHas sentMessages/receivedMessages
    $allCustomers = User::whereIn('role', ['user', 'poster'])
        ->whereNotNull('email_verified_at')
        ->withCount(['sentMessages as unread_count' => function ($q) use ($authId) {
            $q->where('receiver_id', $authId)->whereNull('read_at');
        }])
        ->orderByDesc('unread_count')
        ->orderBy('name', 'asc')
        ->get();

    // 3. Tách nhóm
    $posters = $allCustomers->where('role', 'poster');
    $users = $allCustomers->where('role', 'user');

    return [$admins, $posters, $users];
}

    // ============================================
    // CÁC FUNCTION CHÍNH
    // ============================================

    // Đánh dấu đã đọc tin nhắn
    private function markAsRead($sender_id, $receiver_id)
    {
        Message::where('sender_id', $sender_id)
            ->where('receiver_id', $receiver_id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    // [VIEW] Admin Index (Trang chủ chat Admin)
    public function adminIndex()
    {
        // Gọi helper để lấy 3 biến
        [$admins, $posters, $users] = $this->getSharedChatLists(Auth::id());

        // Truyền đủ 3 biến vào view -> SỬA ĐƯỢC LỖI UNDEFINED VARIABLE
        return view('admin.live_chat.index', compact('admins', 'posters', 'users'));
    }

    // [VIEW] Admin Chat Detail (Khi click vào một người)
    public function adminChat(User $user, Request $request)
    {
        $this->markAsRead($user->id, Auth::id());

        $messages = Message::where(function ($q) use ($user) {
            $q->where('sender_id', Auth::id())->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($user) {
            $q->where('sender_id', $user->id)->where('receiver_id', Auth::id());
        })->orderBy('created_at', 'asc')->get();

        // Nếu là Ajax (chỉ load khung chat phải)
        if ($request->ajax()) {
            return view('admin.live_chat.chat_content', compact('messages', 'user'))->render();
        }

        // Nếu load cả trang -> Cần sidebar -> Gọi helper lấy 3 biến
        [$admins, $posters, $users] = $this->getSharedChatLists(Auth::id());

        return view('admin.live_chat.index', compact('admins', 'posters', 'users', 'messages', 'user'));
    }

    // [API] Lấy danh sách chat (JSON) cho JS
    public function getChatList(Request $request)
    {
        $user = Auth::user();
        $scope = $request->input('scope', 'public');

        // LOGIC 1: Admin ở Dashboard
        if ($user->role === 'admin' && $scope === 'admin_dashboard') {
            
            [$admins, $posters, $users] = $this->getSharedChatLists($user->id);

            return response()->json([
                'mode' => 'admin_dashboard',
                'admins' => $admins->values(),   // values() để reset index array cho JSON đẹp
                'posters' => $posters->values(),
                'users' => $users->values()
            ]);
        }

        // LOGIC 2: Admin ở Public hoặc User thường
        else {
            // Logic cũ: Chỉ hiển thị danh sách Admin để hỗ trợ
            $lastMessageQuery = Message::select('created_at')
                ->where(function ($q) use ($user) {
                    $q->where('sender_id', $user->id)->whereColumn('receiver_id', 'users.id');
                })
                ->orWhere(function ($q) use ($user) {
                    $q->where('receiver_id', $user->id)->whereColumn('sender_id', 'users.id');
                })
                ->latest()
                ->limit(1);

            $listAdmins = User::where('role', 'admin')
                ->where('id', '!=', $user->id)
                ->addSelect(['last_interaction' => $lastMessageQuery])
                ->withCount(['sentMessages as unread_count' => function ($q) use ($user) {
                    $q->where('receiver_id', $user->id)->whereNull('read_at');
                }])
                ->orderByDesc('last_interaction')
                ->orderBy('name', 'asc')
                ->get();

            // Trả về key 'users' chung chung (vì JS bên public đang dùng key này)
            return response()->json([
                'mode' => 'public',
                'users' => $listAdmins 
            ]);
        }
    }

    // [API] Tìm kiếm User (Cho thanh Search mới)
    public function searchChatUsers(Request $request)
    {
        $search = $request->input('query');
        $authId = Auth::id();

        // Query tất cả user (trừ bản thân)
        $query = User::where('id', '!=', $authId)
            ->whereNotNull('email_verified_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $allResults = $query->withCount(['sentMessages as unread_count' => function ($q) use ($authId) {
                $q->where('receiver_id', $authId)->whereNull('read_at');
            }])
            ->orderByDesc('unread_count')
            ->orderBy('name', 'asc')
            ->get();

        // Chia nhóm kết quả tìm kiếm
        return response()->json([
            'admins' => $allResults->where('role', 'admin')->values(),
            'posters' => $allResults->where('role', 'poster')->values(),
            'users' => $allResults->where('role', 'user')->values(),
        ]);
    }

    // ============================================
    // PUBLIC CHAT FUNCTIONS
    // ============================================

    public function getMessages(Request $request)
    {
        $user = Auth::user();
        $receiverId = $request->input('receiver_id');

        if (!$receiverId) {
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

        // Kiểm tra quyền
        if ($user->role === 'user' || $user->role === 'poster') {
            if ($receiver->role !== 'admin') {
                return response()->json(['error' => 'Bạn chỉ có thể chat với admin'], 403);
            }
            if (!$user->hasVerifiedEmail()) {
                return response()->json(['error' => 'Vui lòng xác thực email để sử dụng chat'], 403);
            }
        } elseif ($user->role === 'admin') {
            if ($receiver->id === $user->id) {
                return response()->json(['error' => 'Bạn không thể chat với chính mình'], 403);
            }
        }

        $this->markAsRead($receiverId, $user->id);

        $messages = Message::where(function ($q) use ($user, $receiverId) {
            $q->where('sender_id', $user->id)->where('receiver_id', $receiverId);
        })->orWhere(function ($q) use ($user, $receiverId) {
            $q->where('sender_id', $receiverId)->where('receiver_id', $user->id);
        })->orderBy('created_at', 'asc')->get();

        return view('user.live_chat.chat_content', compact('messages', 'receiver'))->render();
    }

    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message'     => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $receiver = User::findOrFail($request->receiver_id);

        // Validate quyền gửi
        if ($user->role === 'user' || $user->role === 'poster') {
            if ($receiver->role !== 'admin') {
                return response()->json(['error' => 'Bạn chỉ có thể chat với admin'], 403);
            }
            if (!$user->hasVerifiedEmail()) {
                return response()->json(['error' => 'Vui lòng xác thực email để sử dụng chat'], 403);
            }
        } elseif ($user->role === 'admin') {
            if ($receiver->id === $user->id) {
                return response()->json(['error' => 'Bạn không thể chat với chính mình'], 403);
            }
        }

        $message = Message::create([
            'sender_id'   => $user->id,
            'receiver_id' => $request->receiver_id,
            'message'     => $request->message,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message->load('sender')
            ]);
        }

        return back();
    }

    // ============================================
    // AI CHAT - PHIÊN BẢN SỬA LỖI
    // ============================================

    /**
     * System prompt được cải thiện
     */
    private function getSystemPrompt()
    {
        return "Bạn là AI tư vấn truyện của TruyenVH - website đọc truyện tranh online.

NHIỆM VỤ:
- Gợi ý truyện tranh phù hợp với sở thích người dùng
- Trả lời ngắn gọn, thân thiện
- LUÔN gợi ý TÊN TRUYỆN CỤ THỂ từ danh sách

QUY TẮC:
1. LUÔN trả về tên truyện theo định dạng: [[Tên Truyện|slug]]
    Ví dụ: Hãy đọc thử [[Đảo Hải Tặc|one-piece]] nhé.
2. CHỈ tư vấn về truyện tranh
3. TỪ CHỐI lịch sự nếu hỏi ngoài phạm vi
4. Trả lời NGẮN GỌN (2-4 câu)
5. LUÔN kèm tên truyện cụ thể

PHONG CÁCH:
- Thân thiện, nhiệt tình
- Gợi ý 2-3 bộ truyện
- Giải thích ngắn gọn tại sao nên đọc";
    }

    /**
     * Lấy top truyện từ database - ƯU TIÊN TRUYỆN CÓ ĐỦ DỮ LIỆU
     */
    private function getComicsContext($limit = 30)
    {
        // Lấy truyện có đủ thông tin (rating > 0, có categories, có mô tả)
        $query = Comic::with('categories')
            ->where('approval_status', 'approved')
            ->whereNotNull('description');
        $comics = $query->orderByRaw('(views + follows * 10) DESC')
            ->take($limit)
            ->get();

        if ($comics->isEmpty()) {
            // Fallback: Lấy bất kỳ truyện nào
            $comics = Comic::with('categories')
                ->orderBy('views', 'desc')
                ->take($limit)
                ->get();
        }

        $context = "📚 DANH SÁCH TRUYỆN:\n\n";

        foreach ($comics as $comic) {
            $categories = $comic->categories->pluck('name')->join(', ');

            $context .= sprintf(
                "• %s\n" .
                    "  Thể loại: %s | Tác giả: %s | %s\n" .
                    "  👁️ %s lượt xem | ❤️ %s theo dõi | ⭐ %.1f/5\n" .
                    "  Mô tả: %s\n\n",
                $comic->title,
                $comic->slug,
                $categories ?: 'Đa dạng',
                $comic->author ?: 'Đang cập nhật',
                $this->getStatusText($comic->status),
                number_format($comic->views ?? 0),
                number_format($comic->follows ?? 0),
                $comic->rating ?? 0,
                $this->truncate($comic->description, 80)
            );
        }

        return $context;
    }

    /**
     * Phát hiện intent - CẢI THIỆN
     */
    private function detectIntent($message)
    {
        $message = mb_strtolower($message);

        // Từ khóa truyện - BỔ SUNG THÊM
        $comicKeywords = [
            'truyện',
            'manga',
            'chap',
            'chapter',
            'thể loại',
            'genre',
            'tác giả',
            'author',
            'đọc',
            'xem',
            'gợi ý',
            'recommend',
            'tìm',
            'search',
            'hay',
            'hot',
            'mới',
            'update',
            'hành động',
            'tình cảm',
            'hài',
            'kinh dị',
            'fantasy',
            'romance',
            'action',
            'drama',
            'comedy',
            'horror',
            'adventure',
            'hoàn thành',
            'đang tiến hành',
            'ongoing',
            'completed',
            'thú vị',
            'hấp dẫn',
            'cuốn',
            'xuất sắc',
            'đỉnh',
            'ngon'
        ];

        // Blacklist - BỔ SUNG
        $offTopicKeywords = [
            'chính trị',
            'tổng thống',
            'thủ tướng',
            'bầu cử',
            'lập trình',
            'code',
            'python',
            'javascript',
            'database',
            'toán',
            'vật lý',
            'hóa',
            'sinh học',
            'covid',
            'vaccine',
            'bệnh',
            'bóng đá',
            'world cup',
            'bitcoin',
            'crypto',
            'chứng khoán',
            'thời tiết',
            'weather',
            'nấu ăn',
            'công thức',
            'tại sao',
            'why',
            'làm sao',
            'how to' // Câu hỏi chung
        ];

        // Kiểm tra blacklist
        foreach ($offTopicKeywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return 'off_topic';
            }
        }

        // Kiểm tra comic keywords
        foreach ($comicKeywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return 'comic_related';
            }
        }

        // Câu ngắn (<5 ký tự) hoặc chỉ chào hỏi
        if (
            mb_strlen($message) < 5 ||
            preg_match('/^(hi|hello|hey|alo|chào|xin chào)$/i', $message)
        ) {
            return 'unclear';
        }

        // Mặc định: off-topic nếu không match
        return 'off_topic';
    }

    /**
     * Tìm truyện - CẢI THIỆN
     */
    private function searchRelevantComics($message, $limit = 5)
    {
        $query = Comic::where('approval_status', 'approved')->with('categories');
        $message = mb_strtolower($message);

        $keywords = array_filter(explode(' ', $message), function ($w) {
            return mb_strlen($w) > 2 && !in_array($w, ['truyện', 'truyen', 'tìm', 'xem', 'đọc']);
        });

        if (!empty($keywords)) {
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->orWhere('title', 'like', "%{$word}%");
                    $q->orWhere('slug', 'like', "%{$word}%");
                }
            });
        }

        // Map thể loại
        $genreMap = [
            'hành động' => ['Action', 'Hành Động', 'Võ Thuật'],
            'tình cảm' => ['Romance', 'Tình Cảm'],
            'hài' => ['Comedy', 'Hài Hước'],
            'kinh dị' => ['Horror', 'Kinh Dị'],
            'phiêu lưu' => ['Adventure', 'Phiêu Lưu'],
            'học đường' => ['School Life', 'Học Đường'],
            'fantasy' => ['Fantasy', 'Huyền Huyễn'],
            'drama' => ['Drama', 'Kịch'],
        ];

        // Tìm theo thể loại
        foreach ($genreMap as $vi => $enList) {
            if (str_contains($message, $vi)) {
                $query->whereHas('categories', function ($q) use ($enList) {
                    $q->whereIn('name', $enList);
                });
                break;
            }
        }

        // Tìm theo trạng thái
        if (str_contains($message, 'hoàn thành')) {
            $query->where('status', 'completed');
        } elseif (str_contains($message, 'đang')) {
            $query->where('status', 'ongoing');
        }

        // Lấy kết quả - ƯU TIÊN CHẤT LƯỢNG
        $comics = $query
            ->orderByDesc('views')
            ->orderBy('rating', 'desc')
            ->orderBy('follows', 'desc')
            ->take($limit)
            ->get();

        // Fallback
        if ($comics->isEmpty()) {
            $comics = Comic::where('rating', '>=', 0)
                ->orderBy('views', 'desc')
                ->take($limit)
                ->get();
        }

        return $comics;
    }

    /**
     * Main AI function - SỬA LỖI KẾT NỐI
     */
    public function askAI(Request $request)
    {
        $message = $request->input('message');
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'error' => 'Hệ thống AI chưa được cấu hình. Vui lòng liên hệ admin!'
            ]);
        }

        // Phát hiện intent
        $intent = $this->detectIntent($message);

        Log::info('AI Chat', [
            'message' => $message,
            'intent' => $intent,
            'user' => Auth::id()
        ]);

        // Xử lý off-topic
        if ($intent === 'off_topic') {
            return response()->json([
                'success' => true,
                'reply' => "Mình chỉ biết về truyện tranh thôi! 😊\nBạn muốn tìm truyện thể loại nào? Hành động, tình cảm, hay kinh dị?"
            ]);
        }

        // Xử lý unclear
        if ($intent === 'unclear') {
            return response()->json([
                'success' => true,
                'reply' => "Chào bạn! Mình là AI tư vấn truyện của TruyenVH\n\nHãy cho mình biết bạn muốn tìm truyện gì nhé!\nVí dụ: 'Gợi ý truyện hành động hay'"
            ]);
        }

        try {
            // Chuẩn bị context
            $comicsContext = $this->getComicsContext(30);
            $relevantComics = $this->searchRelevantComics($message, 5);

            $relevantContext = "🎯 TRUYỆN PHÙ HỢP:\n\n";
            foreach ($relevantComics as $comic) {
                $relevantContext .= sprintf(
                    "• %s (%s) - ⭐%.1f\n",
                    $comic->title,
                    $comic->categories->pluck('name')->take(2)->join(', '),
                    $comic->rating ?? 0
                );
            }

            // Tạo prompt
            $fullPrompt = $this->getSystemPrompt() . "\n\n" .
                $comicsContext . "\n" .
                $relevantContext . "\n\n" .
                "CÂU HỎI: " . $message . "\n\n" .
                "Trả lời NGẮN GỌN, gợi ý 2-3 bộ truyện CỤ THỂ từ danh sách.";

            // Gọi API với error handling tốt hơn
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

            $response = Http::withoutVerifying()
                ->timeout(60) // Tăng timeout
                ->connectTimeout(30) // Thêm connect timeout
                ->retry(2, 1000) // Retry 2 lần, mỗi lần cách 1s
                ->post($url, [
                    'contents' => [
                        ['parts' => [['text' => $fullPrompt]]]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.8,
                        'maxOutputTokens' => 3000,
                    ]
                ]);

            // Log response để debug
            Log::info('Gemini Response', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body_length' => strlen($response->body())
            ]);

            if ($response->failed()) {
                Log::error('Gemini API Failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'reason' => $response->reason()
                ]);

                // Trả về fallback response
                return $this->getFallbackResponse($relevantComics);
            }

            $data = $response->json();

            if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                Log::error('Invalid Gemini response structure', ['data' => $data]);
                return $this->getFallbackResponse($relevantComics);
            }

            $answer = $data['candidates'][0]['content']['parts'][0]['text'];

            $answerWithLinks = preg_replace_callback('/\[\[(.*?)\|(.*?)\]\]/', function ($matches) {
                $title = $matches[1];
                $slug = $matches[2];
                $url = route('user.comics.show', $slug);

                return "<a href='{$url}' target='_blank' style='color: #0084ff; font-weight: bold; text-decoration: underline;'>{$title}</a>";
            }, $answer);

            return response()->json([
                'success' => true,
                'reply' => $answerWithLinks
            ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Connection Exception', ['error' => $e->getMessage()]);
            return $this->getFallbackResponse($relevantComics ?? null);
        } catch (\Exception $e) {
            Log::error('AI Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->getFallbackResponse($relevantComics ?? null);
        }
    }

    /**
     * Fallback response khi API lỗi
     */
    private function getFallbackResponse($comics = null)
    {
        // 1. Nếu danh sách truyền vào bị rỗng/null, hãy tự lấy truyện Hot từ DB
        if (!$comics || $comics->count() === 0) {
            // Import model Comic ở đầu file nếu chưa có
            $comics = \App\Models\Comic::where('approval_status', 'approved')
                ->orderBy('views', 'desc') // Lấy truyện nhiều view nhất
                ->take(3)
                ->get();
        }

        // 2. Kiểm tra lại lần nữa (đề phòng DB chưa có truyện nào luôn)
        if ($comics && $comics->count() > 0) {
            $reply = "Hiện tại hệ thống AI đang bận, nhưng mình gợi ý bạn vài bộ truyện Hot nhé:\n\n";

            foreach ($comics->take(3) as $index => $comic) {
                // 1. Tạo URL bằng hàm route của Laravel
                $url = route('user.comics.show', $comic->slug);

                // 2. Tạo thẻ A HTML (Style màu xanh, đậm để dễ click)
                $titleLink = "<a href='{$url}' target='_blank' style='color: #0084ff; font-weight: bold; text-decoration: underline;'>{$comic->title}</a>";

                // 3. Format nội dung (Dùng <br> để xuống dòng vì đây là HTML)
                $reply .= sprintf(
                    "%d. %s<br>&nbsp;&nbsp; 🏷️ %s - ⭐%.1f/5<br>&nbsp;&nbsp; 📝 %s<br><br>",
                    $index + 1,
                    $titleLink, // Đã thay thế tiêu đề bằng Link
                    $comic->categories ? $comic->categories->pluck('name')->take(2)->join(', ') : 'Truyện tranh',
                    $comic->rating ?? 0,
                    $this->truncate($comic->description ?? '', 60)
                );
            }

            $reply .= "Chúc bạn đọc truyện vui vẻ!";

            return response()->json([
                'success' => true,
                'reply' => $reply
            ]);
        }

        // 3. Chỉ khi nào DB web của bạn không có truyện nào mới hiện lỗi này
        return response()->json([
            'success' => false,
            'error' => 'Hệ thống đang bảo trì. Vui lòng quay lại sau!'
        ]);
    }

    // Helper functions
    private function getStatusText($status)
    {
        return match ($status) {
            'ongoing' => 'Đang tiến hành',
            'completed' => 'Hoàn thành',
            'paused' => 'Tạm dừng',
            default => 'Không rõ'
        };
    }

    private function truncate($text, $length = 100)
    {
        if (!$text) return 'Chưa có mô tả';
        $text = strip_tags($text);
        return mb_strlen($text) > $length ? mb_substr($text, 0, $length) . '...' : $text;
    }
}
