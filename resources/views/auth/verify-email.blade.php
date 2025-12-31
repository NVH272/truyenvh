@extends('layouts.app')

@section('title', 'Xác thực Email - TruyenVH')

@section('content')
<style>
    /* --- THAY ĐỔI FONT CHỮ HỖ TRỢ TIẾNG VIỆT --- */
    @import url('https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@300;400;500;600;700&display=swap');

    /* Font riêng cho trang này */
    .verify-wrapper {
        font-family: 'Chakra Petch', sans-serif;
    }

    /* Stylized Input */
    .sg-input {
        background: rgba(255, 255, 255, 0.8);
        border-bottom: 2px solid #333;
        transition: all 0.3s;
        font-weight: 500;
    }

    .sg-input:focus {
        background: #fff;
        border-bottom-color: #d97706;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        outline: none;
    }

    /* Glitch Text Effect */
    .btn-glitch:hover {
        text-shadow: 2px 0 #f00, -2px 0 #00f;
    }
</style>

<div class="flex items-center justify-center w-full py-10 verify-wrapper">

    <!-- Verify Email Form Container -->
    <div class="relative z-10 w-full max-w-md">

        <!-- Stylized Card -->
        <div class="bg-white/90 backdrop-blur-sm shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] border-2 border-black p-8 transform -rotate-1 hover:rotate-0 transition duration-300">

            <!-- Header -->
            <div class="mb-8 text-center">
                <h2 class="text-2xl font-bold text-gray-800 tracking-wider uppercase">Future Gadget Lab</h2>
                <p class="text-gray-500 text-xs mt-1 uppercase font-semibold">Xác thực Email</p>
                <div class="w-16 h-1 bg-black mx-auto mt-2"></div>
            </div>

            <!-- Success Message -->
            @if (session('success'))
            <div class="mb-6 bg-green-100 border-l-4 border-green-600 p-4 text-green-700 text-sm shadow-md">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
            @endif

            @if (session('info'))
            <div class="mb-6 bg-blue-100 border-l-4 border-blue-600 p-4 text-blue-700 text-sm shadow-md">
                <i class="fas fa-info-circle mr-2"></i>
                {{ session('info') }}
            </div>
            @endif

            @if (Auth::user()->hasVerifiedEmail())
            <!-- Đã xác thực -->
            <div class="mb-6 bg-green-100 border-l-4 border-green-600 p-4 text-green-700 text-sm shadow-md">
                <div class="font-bold mb-2">
                    <i class="fas fa-check-circle mr-2"></i>
                    Email đã được xác thực!
                </div>
                <p>Email của bạn đã được xác thực thành công. Bạn có thể sử dụng đầy đủ các chức năng của TruyenVH.</p>
            </div>
            @else
            <!-- Chưa xác thực -->
            <div class="mb-6 bg-yellow-100 border-l-4 border-yellow-600 p-4 text-yellow-700 text-sm shadow-md">
                <div class="font-bold mb-2">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Email chưa được xác thực
                </div>
                <p class="mb-3">Vui lòng kiểm tra hộp thư đến và nhấp vào liên kết xác thực để kích hoạt tài khoản.</p>
                <p class="text-xs opacity-75">Nếu bạn không thấy email, vui lòng kiểm tra thư mục spam hoặc thư rác, hoặc nhấn nút "Gửi lại email xác thực" bên dưới.</p>
            </div>

            <!-- Resend Email Form -->
            <form action="{{ route('verification.send') }}" method="POST" class="space-y-5">
                @csrf

                <div class="text-center text-sm text-gray-600 mb-4">
                    <p>Chưa nhận được email?</p>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full mt-4 bg-gray-900 text-white font-bold py-3 px-4 border-2 border-transparent hover:bg-white hover:text-black hover:border-black transition duration-300 shadow-lg btn-glitch uppercase tracking-widest">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Gửi lại email xác thực
                </button>
            </form>
            @endif

            <!-- Links -->
            <div class="text-center mt-6 flex flex-col space-y-2 text-xs text-gray-500 font-medium">
                <a href="{{ route('home') }}" class="text-blue-600 font-bold hover:underline hover:text-orange-500">
                    <i class="fas fa-home mr-1"></i>
                    Về trang chủ
                </a>
            </div>
        </div>

        <!-- Footer Text Decoration -->
        <div class="text-center mt-8 opacity-60 font-sg">
            <p class="text-[10px] uppercase tracking-[0.3em]">Operation Skuld</p>
        </div>
    </div>
</div>
@endsection