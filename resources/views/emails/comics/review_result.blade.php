<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
</head>

<body style="font-family: Arial, sans-serif; background:#f4f6f8; padding:20px">
    <div style="max-width:600px; margin:auto; background:white; padding:24px; border-radius:8px">

        <h2>
            {{ $result === 'approved'
                ? 'Truyện đã được phê duyệt'
                : 'Truyện đã bị từ chối'
            }}
        </h2>

        <p>
            Xin chào <strong>{{ $comic->creator->name }}</strong>,
        </p>

        <p>
            Truyện <strong>{{ $comic->title }}</strong>
            @if($result === 'approved')
            đã được quản trị viên <strong>phê duyệt</strong> và chính thức hiển thị trên hệ thống.
            @else
            đã bị <strong>từ chối</strong>.
            @endif
        </p>

        {{-- HIỂN THỊ LÝ DO TỪ CHỐI --}}
        @if($result === 'rejected')
        <p><strong>Lý do từ chối:</strong></p>

        <blockquote style="
                background:#fff1f2;
                padding:12px;
                border-left:4px solid #ef4444;
                color:#7f1d1d;
                margin:12px 0;
                border-radius:4px;
            ">
            {{ $comic->rejection_reason ?: 'Không có lý do cụ thể từ quản trị viên.' }}
        </blockquote>
        @endif

        <p style="margin-top:24px">
            Nếu bạn có thắc mắc, vui lòng chỉnh sửa nội dung và gửi lại để được xét duyệt lần tiếp theo.
        </p>

        <p style="margin-top:24px">
            Trân trọng,<br>
            <strong>TruyenVH Team</strong>
        </p>
    </div>
</body>

</html>