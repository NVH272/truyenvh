@extends('layouts.app')

@section('title', 'Điều Khoản Dịch Vụ')

@section('content')
<div class="max-w-5xl mx-auto px-4 pb-12">

    <nav class="flex text-xs md:text-sm text-gray-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-blue-600 transition">Trang Chủ</a>
        <span class="mx-2">/</span>
        <span class="text-blue-600 font-medium">Điều Khoản Dịch Vụ</span>
    </nav>

    <div class="mb-8 text-left border-b border-gray-200 pb-4">
        <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-2">
            Điều Khoản <span class="text-blue-600">Dịch Vụ</span>
        </h1>
        <p class="text-sm text-gray-500">Vui lòng đọc kỹ các điều khoản trước khi sử dụng dịch vụ.</p>
    </div>

    <div class="space-y-8 text-gray-700 leading-relaxed text-sm md:text-base">

        <section>
            <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-3 flex items-center">
                <span class="bg-blue-100 text-blue-600 w-6 h-6 rounded-full flex items-center justify-center mr-2 text-xs font-bold">1</span>
                Giới Thiệu
            </h2>
            <p>
                Chào mừng bạn đến với <span class="font-bold text-gray-900">TruyenVH</span>! Bằng cách truy cập và sử dụng trang web này, bạn đồng ý tuân thủ các điều khoản và điều kiện sử dụng sau đây. Nếu bạn không đồng ý với bất kỳ điều khoản nào, vui lòng không tiếp tục sử dụng dịch vụ của chúng tôi.
            </p>
        </section>

        <section>
            <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-3 flex items-center">
                <span class="bg-blue-100 text-blue-600 w-6 h-6 rounded-full flex items-center justify-center mr-2 text-xs font-bold">2</span>
                Quyền Và Trách Nhiệm Của Người Dùng
            </h2>
            <ul class="list-disc pl-5 md:pl-8 space-y-1 marker:text-blue-500">
                <li>Bạn phải từ <strong class="text-gray-900">13 tuổi trở lên</strong> để sử dụng trang web.</li>
                <li>Bạn cam kết không sử dụng <span class="font-bold">TruyenVH</span> vào mục đích vi phạm pháp luật hoặc gây hại cho cá nhân, tổ chức khác.</li>
                <li>Không đăng tải, chia sẻ nội dung vi phạm bản quyền, nội dung phản cảm hoặc trái với thuần phong mỹ tục.</li>
                <li>Bạn có trách nhiệm bảo vệ thông tin tài khoản của mình và chịu trách nhiệm với mọi hoạt động diễn ra trên tài khoản đó.</li>
            </ul>
        </section>

        <section>
            <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-3 flex items-center">
                <span class="bg-blue-100 text-blue-600 w-6 h-6 rounded-full flex items-center justify-center mr-2 text-xs font-bold">3</span>
                Nội Dung Và Bản Quyền
            </h2>
            <ul class="list-disc pl-5 md:pl-8 space-y-1 marker:text-blue-500">
                <li><span class="font-bold">TruyenVH</span> là nền tảng cung cấp truyện tranh trực tuyến. Chúng tôi <strong class="text-red-600">không sở hữu bản quyền</strong> của các truyện đăng tải trên trang web trừ khi có quy định cụ thể.</li>
                <li>Nếu bạn là chủ sở hữu bản quyền và muốn yêu cầu gỡ bỏ nội dung, vui lòng liên hệ với chúng tôi qua email: <a href="mailto:contact.truyenvh@gmail.com" class="text-blue-600 font-bold hover:underline">contact.truyenvh@gmail.com</a>.</li>
            </ul>
        </section>

        <section>
            <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-3 flex items-center">
                <span class="bg-blue-100 text-blue-600 w-6 h-6 rounded-full flex items-center justify-center mr-2 text-xs font-bold">4</span>
                Trách Nhiệm Pháp Lý
            </h2>
            <ul class="list-disc pl-5 md:pl-8 space-y-1 marker:text-blue-500">
                <li>Chúng tôi không chịu trách nhiệm với bất kỳ thiệt hại nào phát sinh do việc sử dụng hoặc không thể sử dụng dịch vụ của <span class="font-bold">TruyenVH</span>.</li>
                <li>Chúng tôi có quyền <strong class="text-gray-900">thay đổi, tạm ngừng hoặc ngừng cung cấp dịch vụ</strong> mà không cần thông báo trước.</li>
            </ul>
        </section>

        <section>
            <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-3 flex items-center">
                <span class="bg-blue-100 text-blue-600 w-6 h-6 rounded-full flex items-center justify-center mr-2 text-xs font-bold">5</span>
                Chính Sách Quảng Cáo Và Liên Kết
            </h2>
            <ul class="list-disc pl-5 md:pl-8 space-y-1 marker:text-blue-500">
                <li><span class="font-bold">TruyenVH</span> có thể chứa quảng cáo hoặc liên kết đến bên thứ ba. Chúng tôi <strong class="text-gray-900">không chịu trách nhiệm</strong> với nội dung, sản phẩm hoặc dịch vụ của các bên thứ ba này.</li>
                <li>Việc sử dụng các dịch vụ hoặc sản phẩm của bên thứ ba phải tuân theo chính sách của bên đó.</li>
            </ul>
        </section>

        <section>
            <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-3 flex items-center">
                <span class="bg-blue-100 text-blue-600 w-6 h-6 rounded-full flex items-center justify-center mr-2 text-xs font-bold">6</span>
                Tài Khoản Thành Viên
            </h2>
            <ul class="list-disc pl-5 md:pl-8 space-y-1 marker:text-blue-500">
                <li>Khi đăng ký tài khoản trên <span class="font-bold">TruyenVH</span>, bạn phải cung cấp thông tin chính xác và đầy đủ.</li>
                <li>Chúng tôi có quyền tạm khóa hoặc xóa tài khoản của bạn nếu phát hiện vi phạm điều khoản dịch vụ.</li>
            </ul>
        </section>

        <section>
            <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-3 flex items-center">
                <span class="bg-blue-100 text-blue-600 w-6 h-6 rounded-full flex items-center justify-center mr-2 text-xs font-bold">7</span>
                Sửa Đổi Điều Khoản
            </h2>
            <ul class="list-disc pl-5 md:pl-8 space-y-1 marker:text-blue-500">
                <li>Chúng tôi có thể thay đổi hoặc cập nhật điều khoản dịch vụ bất cứ lúc nào mà không cần thông báo trước.</li>
                <li>Người dùng có trách nhiệm kiểm tra điều khoản thường xuyên để cập nhật các thay đổi.</li>
            </ul>
            <p class="text-xs text-gray-500 mt-4 italic">
                Cảm ơn bạn đã sử dụng <strong class="text-gray-900">TruyenVH</strong>!
            </p>
        </section>
    </div>
</div>
@endsection