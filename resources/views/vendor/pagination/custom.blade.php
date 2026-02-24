@if ($paginator->hasPages())
<style>
    /* Ẩn mũi tên tăng giảm số của input type="number" */
    .no-spinners::-webkit-inner-spin-button,
    .no-spinners::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .no-spinners {
        -moz-appearance: textfield;
    }

    /* Cấu hình animation cho input chuyển trang */
    .page-input-transition {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>

<nav class="flex items-center justify-center gap-1.5 md:gap-2 font-sans my-8" aria-label="Pagination">

    {{-- ========================================== --}}
    {{-- NÚT PREVIOUS (Mũi tên trái)                --}}
    {{-- ========================================== --}}
    @if ($paginator->onFirstPage())
    <span class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-300 cursor-not-allowed select-none">
        <i class="fas fa-arrow-left text-[15px]"></i>
    </span>
    @else
    <a href="{{ $paginator->previousPageUrl() }}"
        class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-all duration-200 select-none">
        <i class="fas fa-arrow-left text-[15px]"></i>
    </a>
    @endif

    {{-- ========================================== --}}
    {{-- CÁC SỐ TRANG                               --}}
    {{-- ========================================== --}}
    <div class="flex items-center gap-1">
        @foreach ($elements as $element)

        {{-- Dấu 3 chấm (Click để nhập số) --}}
        @if (is_string($element))
        <div class="relative flex items-center justify-center h-10 group js-ellipsis-container" style="width: 48px;">

            {{-- Trạng thái tĩnh: Nút hiển thị dấu ... --}}
            <button type="button"
                class="w-full h-full rounded-lg text-slate-500 font-bold text-lg hover:bg-slate-100 hover:text-slate-800 transition-all js-ellipsis-btn focus:outline-none flex items-center justify-center select-none"
                title="Chuyển đến trang...">
                <span class="mt-[-8px]">...</span> {{-- Đẩy dấu 3 chấm lên một chút cho cân đối --}}
            </button>

            {{-- Trạng thái động: Ô input nhập số (Mặc định ẩn) --}}
            <div class="absolute inset-0 hidden js-ellipsis-input-wrapper z-10 page-input-transition">
                <input type="number" min="1" max="{{ $paginator->lastPage() }}"
                    class="w-full h-full text-center border-2 border-slate-300 rounded-lg focus:border-[#ff6b4a] focus:ring-4 focus:ring-[#ff6b4a]/20 focus:outline-none text-slate-700 font-bold text-sm bg-white shadow-sm no-spinners px-1 js-ellipsis-input"
                    placeholder="...">
            </div>
        </div>
        @endif

        {{-- Hiển thị các block trang (1, 2, 3...) --}}
        @if (is_array($element))
        @foreach ($element as $page => $url)
        @if ($page == $paginator->currentPage())
        {{-- TRANG HIỆN TẠI (Active - Màu cam) --}}
        <span class="min-w-[40px] h-10 px-3 flex items-center justify-center rounded-lg bg-[#ff6b4a] text-white font-bold text-[15px] shadow-[0_4px_10px_rgba(255,107,74,0.3)] select-none pointer-events-none">
            {{ $page }}
        </span>
        @else
        {{-- CÁC TRANG KHÁC (Inactive) --}}
        <a href="{{ $url }}"
            class="min-w-[40px] h-10 px-3 flex items-center justify-center rounded-lg text-slate-600 font-bold text-[15px] hover:bg-slate-100 hover:text-slate-900 transition-all duration-200 select-none">
            {{ $page }}
        </a>
        @endif
        @endforeach
        @endif
        @endforeach
    </div>

    {{-- ========================================== --}}
    {{-- NÚT NEXT (Mũi tên phải)                    --}}
    {{-- ========================================== --}}
    @if ($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}"
        class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-all duration-200 select-none">
        <i class="fas fa-arrow-right text-[15px]"></i>
    </a>
    @else
    <span class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-300 cursor-not-allowed select-none">
        <i class="fas fa-arrow-right text-[15px]"></i>
    </span>
    @endif

</nav>

{{-- ========================================== --}}
{{-- SCRIPT XỬ LÝ NHẬP TRANG NHANH              --}}
{{-- ========================================== --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ngăn việc bind sự kiện nhiều lần (nếu load ajax)
        if (window.__customPaginationBound) return;
        window.__customPaginationBound = true;

        const bindEllipsisEvents = () => {
            document.querySelectorAll('.js-ellipsis-container').forEach(container => {
                if (container.__bound) return;
                container.__bound = true;

                const btn = container.querySelector('.js-ellipsis-btn');
                const wrapper = container.querySelector('.js-ellipsis-input-wrapper');
                const input = container.querySelector('.js-ellipsis-input');

                if (!btn || !wrapper || !input) return;

                // Mở Input
                btn.addEventListener('click', () => {
                    btn.classList.add('hidden');
                    wrapper.classList.remove('hidden');
                    // Thêm hiệu ứng trượt nhẹ (tuỳ chọn CSS)
                    wrapper.style.transform = 'scale(0.95)';
                    wrapper.style.opacity = '0';

                    setTimeout(() => {
                        wrapper.style.transform = 'scale(1)';
                        wrapper.style.opacity = '1';
                        input.focus();
                    }, 10);
                });

                // Xử lý Enter
                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const page = parseInt(input.value);
                        const maxPage = parseInt(input.getAttribute('max'));

                        if (page > 0 && page <= maxPage) {
                            // Điều hướng, giữ nguyên param cũ (vd: ?filter=newest)
                            const url = new URL(window.location.href);
                            url.searchParams.set('page', page);
                            window.location.href = url.toString();
                        } else {
                            // Lỗi -> Rung nhẹ input
                            wrapper.style.transform = 'translateX(-4px)';
                            setTimeout(() => wrapper.style.transform = 'translateX(4px)', 100);
                            setTimeout(() => wrapper.style.transform = 'translateX(-4px)', 200);
                            setTimeout(() => wrapper.style.transform = 'translateX(0)', 300);
                        }
                    }

                    // Nếu bấm ESC -> Huỷ
                    if (e.key === 'Escape') {
                        closeInput();
                    }
                });

                // Bấm ra ngoài (Blur) -> Đóng input
                input.addEventListener('blur', closeInput);

                function closeInput() {
                    wrapper.classList.add('hidden');
                    btn.classList.remove('hidden');
                    input.value = ''; // Xoá số đang nhập dở
                }
            });
        };

        bindEllipsisEvents();

        // MutationObserver hỗ trợ khi dùng thư viện render HTML bằng AJAX (như Livewire, htmx)
        const observer = new MutationObserver((mutations) => {
            let shouldRebind = false;
            mutations.forEach(mutation => {
                if (mutation.addedNodes.length) shouldRebind = true;
            });
            if (shouldRebind) bindEllipsisEvents();
        });
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });
</script>
@endif