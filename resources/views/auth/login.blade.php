@extends('layouts.app')

@section('content')
<style>
    /* --- CẬP NHẬT FONT CHỮ HỖ TRỢ TIẾNG VIỆT --- */
    @import url('https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@300;400;500;600;700&display=swap');

    /* Font riêng cho trang login */
    .login-wrapper {
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
        /* Màu cam */
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        outline: none;
    }

    /* Glitch Text Effect */
    .btn-glitch:hover {
        text-shadow: 2px 0 #f00, -2px 0 #00f;
    }
</style>

<div class="flex items-center justify-center w-full py-10 login-wrapper">

    <!-- Login Form Container -->
    <div class="relative z-10 w-full max-w-md">

        <!-- Stylized Card (Hiệu ứng xoay rotate-1 đặc trưng) -->
        <div class="bg-white/90 backdrop-blur-sm shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] border-2 border-black p-8 transform rotate-1 hover:rotate-0 transition duration-300">

            <!-- Header -->
            <div class="mb-8 text-center">
                <h2 class="text-2xl font-bold text-gray-800 tracking-wider uppercase">Future Gadget Lab</h2>
                <p class="text-gray-500 text-xs mt-1 uppercase font-semibold">Yêu cầu xác thực thành viên</p>
                <div class="w-16 h-1 bg-black mx-auto mt-2"></div>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
            <div class="mb-6 bg-red-100 border-l-4 border-red-600 p-4 text-red-700 text-sm shadow-md relative overflow-hidden">
                <div class="font-bold mb-1">CẢNH BÁO LỖI:</div>
                <ul class="list-disc pl-4 space-y-1">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <!-- Decorative diagonal lines -->
                <div class="absolute top-0 right-0 w-full h-full opacity-10" style="background: repeating-linear-gradient(45deg, transparent, transparent 10px, #ff0000 10px, #ff0000 20px);"></div>
            </div>
            @endif

            @if (session('success'))
            <div class="mb-6 bg-green-100 border-l-4 border-green-600 p-4 text-green-700 text-sm shadow-md">
                {{ session('success') }}
            </div>
            @endif

            <!-- Form -->
            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Email -->
                <div class="group">
                    <label class="block text-gray-700 text-sm font-bold mb-2 uppercase tracking-wide group-hover:text-orange-600 transition">
                        <span class="mr-2">></span>Email
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full p-3 sg-input text-gray-800 placeholder-gray-400"
                        placeholder="okabe@labmem.com" required>
                </div>

                <!-- Password -->
                <div class="group">
                    <label class="block text-gray-700 text-sm font-bold mb-2 uppercase tracking-wide group-hover:text-orange-600 transition">
                        <span class="mr-2">></span>Mật khẩu
                    </label>
                    <input type="password" name="password"
                        class="w-full p-3 sg-input text-gray-800 placeholder-gray-400"
                        placeholder="*******" required>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <label class="inline-flex items-center cursor-pointer relative group">
                        <input type="checkbox" name="remember" class="sr-only peer">
                        <div class="w-5 h-5 border-2 border-gray-600 peer-checked:bg-gray-800 peer-checked:border-gray-800 transition relative flex items-center justify-center">
                            <!-- Checkmark -->
                            <i class="fas fa-check text-white text-xs opacity-0 peer-checked:opacity-100"></i>
                        </div>
                        <span class="ml-2 text-gray-600 text-sm group-hover:text-black font-medium">Ghi nhớ đăng nhập</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full bg-gray-900 text-white font-bold py-3 px-4 border-2 border-transparent hover:bg-white hover:text-black hover:border-black transition duration-300 shadow-lg btn-glitch uppercase tracking-widest">
                    Khởi động kết nối
                </button>

                <!-- Links -->
                <div class="text-center mt-6 flex flex-col space-y-2 text-xs text-gray-500 font-medium">
                    <p>Chưa là thành viên Lab? <a href="{{ route('register.form') }}" class="text-blue-600 font-bold hover:underline hover:text-orange-500">Đăng ký ngay</a></p>
                    <a href="{{ route('password.request') }}" class="hover:text-gray-800">Quên mật khẩu?</a>
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