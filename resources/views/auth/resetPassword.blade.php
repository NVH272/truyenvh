@extends('layouts.app')

@section('title', 'Đặt Lại Mật Khẩu - Future Gadget Lab')

@section('content')
<style>
    /* --- IMPORT FONTS & STYLES ĐỒNG BỘ --- */
    @import url('https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@300;400;500;600;700&display=swap');

    .reset-wrapper {
        font-family: 'Chakra Petch', sans-serif;
    }

    .font-sg {
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
        /* Cam */
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        outline: none;
    }

    /* Glitch Text Effect cho Button */
    .btn-glitch:hover {
        text-shadow: 2px 0 #f00, -2px 0 #00f;
    }
</style>

<div class="flex items-center justify-center w-full py-10 reset-wrapper">

    <!-- Reset Password Container -->
    <div class="relative z-10 w-full max-w-md">

        <!-- Stylized Card (Xoay ngược chiều login 1 chút để tạo nét riêng) -->
        <div class="bg-white/90 backdrop-blur-sm shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] border-2 border-black p-8 transform -rotate-1 hover:rotate-0 transition duration-300">

            <!-- Header -->
            <div class="mb-8 text-center">
                <h2 class="text-2xl font-bold text-gray-800 tracking-wider uppercase">Future Gadget Lab</h2>
                <p class="text-gray-500 text-xs mt-1 uppercase font-semibold">Thiết lập lại Timeline</p>
                <div class="w-16 h-1 bg-black mx-auto mt-2"></div>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
            <div class="mb-6 bg-red-100 border-l-4 border-red-600 p-4 text-red-700 text-sm shadow-md relative overflow-hidden">
                <div class="font-bold mb-1">CẢNH BÁO PHÂN KỲ:</div>
                <ul class="list-disc pl-4 space-y-1">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <!-- Decorative lines -->
                <div class="absolute top-0 right-0 w-full h-full opacity-10"
                    style="background: repeating-linear-gradient(45deg, transparent, transparent 10px, #ff0000 10px, #ff0000 20px);"></div>
            </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ request()->route('token') }}">
                <!-- Email hidden (lấy từ query của link reset) -->
                <input type="hidden" name="email" value="{{ request('email') }}">

                <!-- New Password -->
                <div class="group">
                    <label class="block text-gray-700 text-sm font-bold mb-1 uppercase tracking-wide group-hover:text-orange-600 transition">
                        <span class="mr-2">></span>Mật khẩu mới
                    </label>
                    <input type="password"
                        name="password"
                        class="w-full p-2 sg-input text-gray-800 placeholder-gray-400"
                        placeholder="Nhập mật khẩu mới..." required autocomplete="new-password">
                </div>

                <!-- Confirm Password -->
                <div class="group">
                    <label class="block text-gray-700 text-sm font-bold mb-1 uppercase tracking-wide group-hover:text-orange-600 transition">
                        <span class="mr-2">></span>Xác nhận mật khẩu
                    </label>
                    <input type="password"
                        name="password_confirmation"
                        class="w-full p-2 sg-input text-gray-800 placeholder-gray-400"
                        placeholder="Nhập lại mật khẩu..." required autocomplete="new-password">
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full mt-4 bg-gray-900 text-white font-bold py-3 px-4 border-2 border-transparent hover:bg-white hover:text-black hover:border-black transition duration-300 shadow-lg btn-glitch uppercase tracking-widest">
                    Ghi đè dữ liệu
                </button>

                <!-- Back Link -->
                <div class="text-center mt-6 flex flex-col space-y-2 text-xs text-gray-500 font-medium">
                    <a href="{{ route('login.form') }}" class="text-gray-500 hover:text-blue-600 transition">
                        <i class="fas fa-arrow-left mr-1"></i> Hủy bỏ thao tác
                    </a>
                </div>
            </form>
        </div>

        <!-- Footer Text Decoration -->
        <div class="text-center mt-8 opacity-60 font-sg">
            <p class="text-[10px] uppercase tracking-[0.3em]">El Psy Kongroo</p>
        </div>
    </div>
</div>
@endsection