@props([
    'rating' => 0,
    'max' => 5,
    'fullColor' => 'text-yellow-500',
    'emptyColor' => 'text-slate-300',
    'sizeClass' => 'text-[10px]',
])

@php
    $clamped = max(0, min((float) $rating, (float) $max));
    $percent = $max > 0 ? number_format(($clamped / $max) * 100, 2, '.', '') : '0';
@endphp

<div {{ $attributes->merge(['class' => "relative inline-flex items-center $sizeClass"]) }}>
    <div class="flex {{ $emptyColor }}">
        @for($i = 0; $i < $max; $i++)
            <i class="fas fa-star"></i>
        @endfor
    </div>

    <div class="absolute inset-0 overflow-hidden" style="width: {{ $percent }}%;">
        <div class="flex {{ $fullColor }}">
            @for($i = 0; $i < $max; $i++)
                <i class="fas fa-star"></i>
            @endfor
        </div>
    </div>
</div>
