@extends('layouts.app')

@section('title', 'Phương Thức Liên Hệ')

@section('content')
{{--
       Lưu ý: Không cần tạo thêm div bg-white hay shadow bao ngoài nữa 
       vì layout.app đã tự động bọc @yield('content') trong khung trắng rồi.
       Chúng ta chỉ cần chia cột nội dung bên trong thôi.
    --}}

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-start">

    <div class="flex flex-col justify-center h-full">

        <nav class="flex text-sm text-gray-500 mb-4">
            <a href="{{ route('home') }}" class="hover:text-blue-600 transition">Trang Chủ</a>
            <span class="mx-2">/</span>
            <span class="text-blue-600 font-medium">Liên Hệ</span>
        </nav>

        <h2 class="text-3xl font-bold text-gray-800 mb-6 border-b border-gray-200 pb-4">
            Liên hệ với chúng tôi
        </h2>

        <div class="space-y-6 text-gray-700">
            <div class="flex items-start group">
                <div class="flex-shrink-0 mt-1 p-2 bg-blue-50 rounded-lg group-hover:bg-blue-100 transition">
                    <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Email hỗ trợ</p>
                    <a href="mailto:contact.truyenvh@gmail.com" class="text-lg font-bold text-blue-600 hover:text-blue-800 transition">
                        contact.truyenvh@gmail.com
                    </a>
                    <p class="text-xs text-gray-400 mt-1">Chúng tôi thường phản hồi trong vòng 24h</p>
                </div>
            </div>

            <div class="flex items-start group">
                <div class="flex-shrink-0 mt-1 p-2 bg-blue-50 rounded-lg group-hover:bg-blue-100 transition">
                    <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Địa chỉ văn phòng</p>
                    <span class="text-base font-medium text-gray-800 leading-relaxed block">
                        41A Đ. Phú Diễn, Phú Diễn,<br> Bắc Từ Liêm, Hà Nội, Việt Nam
                    </span>
                </div>
            </div>

            <div class="flex items-start group">
                <div class="flex-shrink-0 mt-1 p-2 bg-blue-50 rounded-lg group-hover:bg-blue-100 transition">
                    <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Hotline</p>
                    <a href="tel:0987654321" class="text-lg font-bold text-blue-600 hover:text-blue-800 transition">
                        0987654321
                    </a>
                    <p class="text-xs text-gray-400 mt-1">Hỗ trợ từ 8:00 - 17:00</p>
                </div>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-100">
            <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 transition">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại trang chủ
            </a>
        </div>
    </div>

    <div class="h-full min-h-[400px] w-full bg-gray-100 rounded-xl overflow-hidden border border-gray-200 shadow-inner relative">
        {{-- Đã cập nhật src có tham số 'q=' để hiển thị ghim đỏ tại địa chỉ --}}
        <iframe
            class="absolute inset-0 w-full h-full border-0"
            src="https://maps.google.com/maps?q=41A%20%C4%90.%20Ph%C3%BA%20Di%E1%BB%85n%2C%20Ph%C3%BA%20Di%E1%BB%85n%2C%20B%E1%BA%AFc%20T%E1%BB%AB%20Li%C3%AAm%2C%20H%C3%A0%20N%E1%BB%99i&t=&z=15&ie=UTF8&iwloc=&output=embed"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>
</div>
@endsection