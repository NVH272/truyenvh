@extends('layouts.app')

@section('title', 'Về Chúng Tôi')

@section('content')
<div class="max-w-5xl mx-auto px-4">

    <nav class="flex text-xs md:text-sm text-gray-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-blue-600 transition">Trang Chủ</a>
        <span class="mx-2">/</span>
        <span class="text-blue-600 font-medium">Về Chúng Tôi</span>
    </nav>

    <div class="mb-8 text-left">
        <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-2">
            Về <span class="text-blue-600">TruyenVH</span>
        </h1>
        <div class="h-1 w-16 bg-blue-600 rounded"></div>
    </div>

    <div class="space-y-10 text-gray-700 leading-relaxed text-sm md:text-base">

        <section>
            <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-3 flex items-center justify-start">
                <span class="bg-blue-100 text-blue-600 w-6 h-6 rounded-full flex items-center justify-center mr-2 text-xs">1</span>
                Chúng Tôi Là Ai?
            </h2>
            <p>
                Chào mừng bạn đến với <span class="font-bold text-gray-900">TruyenVH</span> - nền tảng đọc <a href="#" class="text-blue-600 hover:underline">truyện tranh</a> trực tuyến dành cho tất cả những ai yêu thích thế giới truyện tranh phong phú. Chúng tôi không chỉ là một website đọc truyện mà còn là một cộng đồng dành cho những người đam mê manga, manhua, manhwa và truyện tranh Việt Nam.
            </p>
        </section>

        <section>
            <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-3 flex items-center justify-start">
                <span class="bg-blue-100 text-blue-600 w-6 h-6 rounded-full flex items-center justify-center mr-2 text-xs">2</span>
                Sứ Mệnh Của Chúng Tôi
            </h2>
            <p class="mb-3">Tại <span class="font-bold text-gray-900">TruyenVH</span>, chúng tôi cam kết mang đến cho độc giả:</p>

            {{-- Giảm padding-left để sát lề hơn --}}
            <ul class="space-y-2 pl-0 md:pl-2">
                <li class="flex items-start">
                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                    <span><strong class="text-gray-900">Kho truyện đa dạng:</strong> Cập nhật liên tục các thể loại truyện từ hành động, phiêu lưu, lãng mạn, hài hước cho đến kinh dị, trinh thám.</span>
                </li>
                <li class="flex items-start">
                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                    </svg>
                    <span><strong class="text-gray-900">Trải nghiệm đọc truyện mượt mà:</strong> Giao diện thân thiện, dễ sử dụng, giúp người đọc tận hưởng trọn vẹn nội dung truyện yêu thích.</span>
                </li>
                <li class="flex items-start">
                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                    <span><strong class="text-gray-900">Cộng đồng kết nối:</strong> Tạo không gian giao lưu, chia sẻ giữa những người có chung niềm đam mê truyện tranh.</span>
                </li>
                <li class="flex items-start">
                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                    <span><strong class="text-gray-900">Cộng đồng kết nối:</strong> Tạo không gian giao lưu, chia sẻ giữa những người có chung niềm đam mê truyện tranh.</span>
                </li>
            </ul>
        </section>

        <section>
            <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-3 flex items-center justify-start">
                <span class="bg-blue-100 text-blue-600 w-6 h-6 rounded-full flex items-center justify-center mr-2 text-xs">3</span>
                Giá Trị Cốt Lõi
            </h2>
            <ul class="space-y-2 pl-0 md:pl-2">
                <li class="flex items-start">
                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                    <span><strong class="text-gray-900">Miễn phí & dễ dàng truy cập:</strong> Người dùng có thể đọc truyện thoải mái mà không cần đăng ký tài khoản.</span>
                </li>
                <li class="flex items-start">
                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                    <span><strong class="text-gray-900">Cập nhật nhanh chóng:</strong> Chúng tôi luôn cố gắng cập nhật những chương truyện mới nhất để phục vụ độc giả.</span>
                </li>
                <li class="flex items-start">
                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                    <span><strong class="text-gray-900">Tôn trọng bản quyền:</strong> Chúng tôi luôn lắng nghe phản hồi từ các tác giả và nhà xuất bản để đảm bảo quyền lợi của họ.</span>
                </li>
            </ul>
            <p class="text-xs text-gray-500 italic mt-6">
                Cảm ơn bạn đã đồng hành cùng <strong>TruyenVH</strong>!
            </p>
        </section>
    </div>
</div>
@endsection