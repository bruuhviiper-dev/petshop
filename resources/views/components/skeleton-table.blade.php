@props(['rows' => 5, 'cols' => 4])

<div class="animate-pulse space-y-3">
    {{-- Header skeleton --}}
    <div class="flex gap-4 pb-3 border-b border-gray-100 dark:border-gray-700">
        @for($i = 0; $i < $cols; $i++)
            <x-skeleton class="h-3 {{ $i === 0 ? 'w-1/3' : 'flex-1' }}" />
        @endfor
    </div>
    {{-- Rows skeleton --}}
    @for($r = 0; $r < $rows; $r++)
        <div class="flex gap-4 py-2">
            @for($i = 0; $i < $cols; $i++)
                <x-skeleton class="h-4 {{ $i === 0 ? 'w-1/3' : 'flex-1' }}" />
            @endfor
        </div>
    @endfor
</div>
