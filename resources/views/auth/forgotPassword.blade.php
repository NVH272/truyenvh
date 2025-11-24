@extends('layouts.app')

@section('content')
<style>
    /* --- THAY ĐỔI FONT CHỮ HỖ TRỢ TIẾNG VIỆT --- */
    /* Nhúng font Chakra Petch từ Google Fonts */
    @import url('https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@300;400;500;600;700&display=swap');

    /* Font riêng cho trang này */
    .register-wrapper {
        font-family: 'Chakra Petch', sans-serif;
    }

    /* Stylized Input */
    .sg-input {
        background: rgba(255, 255, 255, 0.8);
        border-bottom: 2px solid #333;
        transition: all 0.3s;
        font-weight: 500;
        /* Tăng độ đậm chút để dễ đọc */
    }

    .sg-input:focus {
        background: #fff;
        border-bottom-color: #d97706;
        /* Màu cam */
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        outline: none;
    }

    /* Glitch Text Effect */
    .btn-glitch:hover {
        text-shadow: 2px 0 #f00, -2px 0 #00f;
    }
</style>

<div class="flex items-center justify-center w-full py-10 auth-wrapper">

    <!-- Container -->
    <div class="relative z-10 w-full max-w-md">

        <!-- Stylized Card (Xoay nhẹ rotate-1) -->
        <div class="bg-white/90 backdrop-blur-sm shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] border-2 border-black p-8 transform rotate-1 hover:rotate-0 transition duration-300">

            <!-- Header -->
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-800 tracking-wider uppercase">Future Gadget Lab</h2>
                <p class="text-gray-500 text-xs mt-1 uppercase font-semibold">Khôi phục mật khẩu</p>
                <div class="w-16 h-1 bg-black mx-auto mt-2"></div>
            </div>

            <div class="text-sm text-gray-600 mb-6 text-justify font-medium leading-relaxed">
                <span class="font-bold text-orange-600">></span> Quên mật khẩu truy cập Lab? Không vấn đề gì. Hãy nhập địa chỉ Email của bạn, chúng tôi sẽ gửi một liên kết để bạn thiết lập lại nó.
            </div>

            <!-- Session Status (Thông báo thành công) -->
            @if (session('status'))
            <div class="mb-6 bg-green-100 border-l-4 border-green-600 p-4 text-green-700 text-sm shadow-md">
                <span class="font-bold">THÀNH CÔNG:</span> {{ session('status') }}
            </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
            <div class="mb-6 bg-red-100 border-l-4 border-red-600 p-4 text-red-700 text-sm shadow-md relative overflow-hidden">
                <div class="font-bold mb-1">LỖI KẾT NỐI:</div>
                <ul class="list-disc pl-4 space-y-1">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <div class="absolute top-0 right-0 w-full h-full opacity-10" style="background: repeating-linear-gradient(45deg, transparent, transparent 10px, #ff0000 10px, #ff0000 20px);"></div>
            </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <!-- Email Address -->
                <div class="group">
                    <label class="block text-gray-700 text-sm font-bold mb-2 uppercase tracking-wide group-hover:text-orange-600 transition">
                        <span class="mr-2">></span>Email
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full p-3 sg-input text-gray-800 placeholder-gray-400"
                        placeholder="okabe@labmem.com" required autofocus>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full bg-gray-900 text-white font-bold py-3 px-4 border-2 border-transparent hover:bg-white hover:text-black hover:border-black transition duration-300 shadow-lg btn-glitch uppercase tracking-widest text-sm">
                    Gửi Email Khôi Phục
                </button>

                <!-- Back to Login -->
                <div class="text-center mt-6 pt-4 border-t border-gray-300 border-dashed">
                    <a href="{{ route('login.form') }}" class="text-xs font-bold text-gray-500 hover:text-blue-600 transition uppercase tracking-wide flex items-center justify-center gap-2">
                        <i class="fas fa-arrow-left"></i> Quay lại đăng nhập
                    </a>
                </div>
            </form>
        </div>

        <!-- Footer Text Decoration -->
        <div class="text-center mt-8 opacity-60 font-sg text-gray-600">
            <p class="text-[10px] uppercase tracking-[0.3em] font-bold">Operation Arclight</p>
        </div>
    </div>
</div>
@endsection