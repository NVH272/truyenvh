@extends('layouts.app')

@section('title', 'Tuyên Bố Miễn Trừ Trách Nhiệm')

@section('content')
<div class="max-w-5xl mx-auto px-4 pb-12">

    <nav class="flex text-xs md:text-sm text-gray-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-blue-600 transition">Trang Chủ</a>
        <span class="mx-2">/</span>
        <span class="text-blue-600 font-medium">Tuyên Bố Miễn Trừ Trách Nhiệm</span>
    </nav>

    <div class="mb-8 text-left border-b border-gray-200 pb-4">
        <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-2">
            Tuyên Bố <span class="text-blue-600">Miễn Trừ Trách Nhiệm</span>
        </h1>
        <p class="text-sm text-gray-500">Vui lòng đọc kỹ thông tin dưới đây.</p>
    </div>

    <div class="space-y-8 text-gray-700 leading-relaxed text-sm md:text-base">

        <section>
            <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-3 flex items-center">
                <span class="bg-blue-100 text-blue-600 w-6 h-6 rounded-full flex items-center justify-center mr-2 text-xs font-bold">1</span>
                Giới Thiệu
            </h2>
            <p>
                Chào mừng bạn đến với <span class="font-bold text-gray-900">TruyenVH</span>! Bằng việc truy cập và sử dụng trang web này, bạn đồng ý với nội dung trong <strong class="text-gray-900">Tuyên Bố Miễn Trừ Trách Nhiệm</strong> này. Nếu bạn không đồng ý với bất kỳ phần nào của tuyên bố, vui lòng ngừng sử dụng dịch vụ của chúng tôi.
            </p>
        </section>

        <section>
            <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-3 flex items-center">
                <span class="bg-blue-100 text-blue-600 w-6 h-6 rounded-full flex items-center justify-center mr-2 text-xs font-bold">2</span>
                Nội Dung Trên Website
            </h2>
            <ul class="list-disc pl-5 md:pl-8 space-y-1 marker:text-blue-500">
                <li><span class="font-bold">TruyenVH</span> là một nền tảng đọc truyện tranh trực tuyến được tổng hợp từ nhiều nguồn khác nhau.</li>
                <li>Chúng tôi <strong class="text-red-600">không sở hữu bản quyền</strong> của bất kỳ truyện nào, trừ khi có quy định cụ thể. Mọi quyền sở hữu trí tuệ thuộc về tác giả và đơn vị xuất bản.</li>
                <li>Nếu bạn là chủ sở hữu bản quyền và cho rằng nội dung trên website vi phạm quyền lợi của bạn, vui lòng liên hệ với chúng tôi qua email: <a href="mailto:contact.truyenvh@gmail.com" class="text-blue-600 font-bold hover:underline">contact.truyenvh@gmail.com</a> để yêu cầu gỡ bỏ nội dung.</li>
            </ul>
        </section>

        <section>
            <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-3 flex items-center">
                <span class="bg-blue-100 text-blue-600 w-6 h-6 rounded-full flex items-center justify-center mr-2 text-xs font-bold">3</span>
                Tính Chính Xác Của Nội Dung
            </h2>
            <ul class="list-disc pl-5 md:pl-8 space-y-1 marker:text-blue-500">
                <li>Chúng tôi không đảm bảo rằng tất cả nội dung trên website là <strong class="text-gray-900">chính xác, đầy đủ hoặc cập nhật</strong>. Nội dung có thể thay đổi, chỉnh sửa hoặc bị xóa bất cứ lúc nào mà không cần thông báo trước.</li>
                <li>Mọi thông tin, hình ảnh, hoặc nội dung đăng tải trên <span class="font-bold">TruyenVH</span> chỉ mang tính chất tham khảo và giải trí.</li>
            </ul>
        </section>

        <section>
            <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-3 flex items-center">
                <span class="bg-blue-100 text-blue-600 w-6 h-6 rounded-full flex items-center justify-center mr-2 text-xs font-bold">4</span>
                Miễn Trừ Trách Nhiệm Pháp Lý
            </h2>
            <ul class="list-disc pl-5 md:pl-8 space-y-1 marker:text-blue-500">
                <li>Chúng tôi không chịu trách nhiệm đối với bất kỳ <strong class="text-gray-900">thiệt hại trực tiếp, gián tiếp, ngẫu nhiên hoặc hậu quả nào</strong> do việc sử dụng hoặc không thể sử dụng dịch vụ của <span class="font-bold">TruyenVH</span>.</li>
                <li>Người dùng tự chịu trách nhiệm khi truy cập và sử dụng nội dung trên website.</li>
                <li>Chúng tôi không đảm bảo rằng website sẽ hoạt động <strong class="text-gray-900">liên tục, không bị gián đoạn hoặc không có lỗi</strong>.</li>
            </ul>
        </section>

        <section>
            <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-3 flex items-center">
                <span class="bg-blue-100 text-blue-600 w-6 h-6 rounded-full flex items-center justify-center mr-2 text-xs font-bold">5</span>
                Liên Kết Đến Bên Thứ Ba
            </h2>
            <ul class="list-disc pl-5 md:pl-8 space-y-1 marker:text-blue-500">
                <li><span class="font-bold">TruyenVH</span> có thể chứa các liên kết đến website của bên thứ ba. Chúng tôi <strong class="text-red-600">không kiểm soát và không chịu trách nhiệm</strong> về nội dung, chính sách bảo mật hoặc hoạt động của các trang web này.</li>
                <li>Việc sử dụng các trang web bên thứ ba hoàn toàn do bạn tự chịu rủi ro.</li>
            </ul>
        </section>

        <section>
            <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-3 flex items-center">
                <span class="bg-blue-100 text-blue-600 w-6 h-6 rounded-full flex items-center justify-center mr-2 text-xs font-bold">6</span>
                Thay Đổi Tuyên Bố Miễn Trừ Trách Nhiệm
            </h2>
            <ul class="list-disc pl-5 md:pl-8 space-y-1 marker:text-blue-500">
                <li>Chúng tôi có quyền sửa đổi hoặc cập nhật nội dung của tuyên bố này bất cứ lúc nào mà không cần thông báo trước.</li>
                <li>Việc tiếp tục sử dụng <span class="font-bold">TruyenVH</span> sau khi có thay đổi đồng nghĩa với việc bạn chấp nhận các điều khoản mới.</li>
            </ul>
            <p class="text-xs text-gray-500 mt-4 italic">
                Cảm ơn bạn đã sử dụng <strong class="text-gray-900">TruyenVH</strong>!
            </p>
        </section>
    </div>
</div>
@endsection