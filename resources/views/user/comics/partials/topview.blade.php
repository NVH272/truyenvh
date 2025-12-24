<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-bold text-gray-800 text-base border-l-4 border-red-500 pl-3 uppercase">
            Top Lượt Xem
        </h3>
        <div class="flex gap-1">
            <button class="text-[10px] px-2 py-0.5 bg-red-500 text-white rounded-full">Ngày</button>
            <button class="text-[10px] px-2 py-0.5 bg-gray-200 text-gray-500 rounded-full hover:bg-gray-300">Tuần</button>
        </div>
    </div>
    <div class="divide-y divide-gray-100">
        @foreach(range(1, 5) as $index => $item)
        <div class="flex items-center gap-3 p-3 hover:bg-gray-50 transition-colors cursor-pointer group">
            <span class="w-6 h-6 flex-shrink-0 flex items-center justify-center rounded-full {{ $index < 3 ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-600' }} text-xs font-bold">
                {{ $index + 1 }}
            </span>
            <div class="w-12 h-16 flex-shrink-0 rounded overflow-hidden">
                <img src="https://placehold.co/80x120?text=Top" class="w-full h-full object-cover" alt="Top">
            </div>
            <div class="flex-1 min-w-0">
                <h4 class="text-sm font-medium text-gray-800 group-hover:text-red-500 truncate transition-colors">
                    Siêu Phẩm Top {{ $index + 1 }}
                </h4>
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-eye text-gray-400"></i> 1.2M
                    <span class="mx-1">•</span>
                    Chapter 200
                </p>
            </div>
        </div>
        @endforeach
    </div>
</div>