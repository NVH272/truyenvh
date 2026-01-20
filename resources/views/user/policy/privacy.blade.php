@extends('layouts.app')

@section('title', 'Chính Sách Bảo Mật')

@section('content')
<div class="max-w-5xl mx-auto px-4 pb-12">

    <nav class="flex text-xs md:text-sm text-gray-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-blue-600 transition">Trang Chủ</a>
        <span class="mx-2">/</span>
        <span class="text-blue-600 font-medium">Chính Sách Bảo Mật</span>
    </nav>

    <div class="mb-8 text-left border-b border-gray-200 pb-4">
        <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-2">
            Chính Sách <span class="text-blue-600">Bảo Mật</span>
        </h1>
        <p class="text-sm text-gray-500">Cập nhật lần cuối: {{ date('d/m/Y') }}</p>
    </div>

    <div class="space-y-8 text-gray-700 leading-relaxed text-sm md:text-base">

        <section>
            <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-3 flex items-center">
                <span class="bg-blue-100 text-blue-600 w-6 h-6 rounded-full flex items-center justify-center mr-2 text-xs font-bold">1</span>
                Giới Thiệu
            </h2>
            <p>
                Chào mừng bạn đến với <span class="font-bold text-gray-900">TruyenVH</span>. Chúng tôi cam kết bảo vệ quyền riêng tư và thông tin cá nhân của bạn khi truy cập và sử dụng dịch vụ trên trang web này. Chính sách bảo mật này giải thích cách chúng tôi thu thập, sử dụng và bảo vệ thông tin của bạn.
            </p>
        </section>

        <section>
            <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-3 flex items-center">
                <span class="bg-blue-100 text-blue-600 w-6 h-6 rounded-full flex items-center justify-center mr-2 text-xs font-bold">2</span>
                Thông Tin Chúng Tôi Thu Thập
            </h2>
            <p class="mb-3">Khi bạn sử dụng <span class="font-bold">TruyenVH</span>, chúng tôi có thể thu thập các loại thông tin sau:</p>
            <ul class="list-disc pl-5 md:pl-8 space-y-1 marker:text-blue-500">
                <li><strong class="text-gray-900">Thông tin cá nhân:</strong> Tên, email, số điện thoại (nếu có cung cấp khi đăng ký hoặc liên hệ với chúng tôi).</li>
                <li><strong class="text-gray-900">Thông tin thiết bị:</strong> Địa chỉ IP, loại trình duyệt, hệ điều hành, thời gian truy cập.</li>
                <li><strong class="text-gray-900">Cookies và công nghệ theo dõi:</strong> Dữ liệu cookie để cải thiện trải nghiệm người dùng và tối ưu hóa nội dung hiển thị.</li>
            </ul>
        </section>

        <section>
            <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-3 flex items-center">
                <span class="bg-blue-100 text-blue-600 w-6 h-6 rounded-full flex items-center justify-center mr-2 text-xs font-bold">3</span>
                Cách Chúng Tôi Sử Dụng Thông Tin
            </h2>
            <p class="mb-3">Chúng tôi sử dụng thông tin thu thập được để:</p>
            <ul class="list-disc pl-5 md:pl-8 space-y-1 marker:text-blue-500">
                <li>Cung cấp, duy trì và cải thiện dịch vụ.</li>
                <li>Đáp ứng yêu cầu hỗ trợ khách hàng.</li>
                <li>Phân tích, thống kê để cải thiện chất lượng nội dung.</li>
                <li>Ngăn chặn các hoạt động gian lận hoặc vi phạm chính sách sử dụng.</li>
            </ul>
        </section>

        <section>
            <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-3 flex items-center">
                <span class="bg-blue-100 text-blue-600 w-6 h-6 rounded-full flex items-center justify-center mr-2 text-xs font-bold">4</span>
                Chia Sẻ Thông Tin Với Bên Thứ Ba
            </h2>
            <p class="mb-3">Chúng tôi <strong class="text-red-600">không</strong> bán, trao đổi hoặc chia sẻ thông tin cá nhân của bạn với bên thứ ba trừ các trường hợp:</p>
            <ul class="list-disc pl-5 md:pl-8 space-y-1 marker:text-blue-500">
                <li>Khi có sự đồng ý của bạn.</li>
                <li>Theo yêu cầu của cơ quan chức năng theo quy định pháp luật.</li>
                <li>Để bảo vệ quyền lợi, tài sản hoặc an toàn của <span class="font-bold">TruyenVH</span> và người dùng.</li>
            </ul>
        </section>

        <section>
            <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-3 flex items-center">
                <span class="bg-blue-100 text-blue-600 w-6 h-6 rounded-full flex items-center justify-center mr-2 text-xs font-bold">5</span>
                Bảo Mật Thông Tin
            </h2>
            <p>
                Chúng tôi áp dụng các biện pháp bảo mật hợp lý để bảo vệ thông tin cá nhân của bạn, bao gồm mã hóa dữ liệu và giới hạn quyền truy cập. Tuy nhiên, không có hệ thống nào an toàn tuyệt đối, vì vậy bạn cần cẩn trọng khi chia sẻ thông tin cá nhân trên môi trường trực tuyến.
            </p>
        </section>

        <section>
            <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-3 flex items-center">
                <span class="bg-blue-100 text-blue-600 w-6 h-6 rounded-full flex items-center justify-center mr-2 text-xs font-bold">6</span>
                Quyền Lợi Của Người Dùng
            </h2>
            <p class="mb-3">Bạn có quyền:</p>
            <ul class="list-disc pl-5 md:pl-8 space-y-1 marker:text-blue-500">
                <li>Yêu cầu truy cập, chỉnh sửa hoặc xóa thông tin cá nhân của mình.</li>
                <li>Từ chối nhận các thông báo quảng cáo từ chúng tôi.</li>
                <li>Hạn chế hoặc phản đối việc xử lý thông tin cá nhân trong một số trường hợp nhất định.</li>
            </ul>
        </section>

        <section>
            <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-3 flex items-center">
                <span class="bg-blue-100 text-blue-600 w-6 h-6 rounded-full flex items-center justify-center mr-2 text-xs font-bold">7</span>
                Cookies và Công Nghệ Theo Dõi
            </h2>
            <p>
                Trang web có thể sử dụng <strong>cookies</strong> để cải thiện trải nghiệm người dùng. Bạn có thể quản lý hoặc từ chối cookie thông qua cài đặt trình duyệt của mình.
            </p>
        </section>

        <section>
            <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-3 flex items-center">
                <span class="bg-blue-100 text-blue-600 w-6 h-6 rounded-full flex items-center justify-center mr-2 text-xs font-bold">8</span>
                Thay Đổi Chính Sách Bảo Mật
            </h2>
            <p>
                Chúng tôi có thể cập nhật chính sách này theo thời gian. Khi có thay đổi, chúng tôi sẽ thông báo trên trang web. Việc tiếp tục sử dụng dịch vụ sau khi chính sách được cập nhật đồng nghĩa với việc bạn đồng ý với các thay đổi đó.
            </p>
            <p class="text-xs text-gray-500 mt-4 italic">
                Cảm ơn bạn đã tin tưởng và sử dụng dịch vụ của <strong>TruyenVH</strong>!
            </p>
        </section>
    </div>
</div>
@endsection