@props(['id', 'title' => '', 'maxWidth' => 'lg'])

@php
$maxWidthClass = [
    'sm'  => 'max-w-sm',
    'md'  => 'max-w-md',
    'lg'  => 'max-w-lg',
    'xl'  => 'max-w-xl',
    '2xl' => 'max-w-2xl',
][$maxWidth] ?? 'max-w-lg';
@endphp

<div x-data="{ open: false }"
     x-on:open-modal.window="if ($event.detail === '{{ $id }}') { open = true; document.body.classList.add('overflow-hidden'); }"
     x-on:close-modal.window="if ($event.detail === '{{ $id }}') { open = false; document.body.classList.remove('overflow-hidden'); }"
     x-on:keydown.escape.window="open = false; document.body.classList.remove('overflow-hidden')"
     x-show="open" x-cloak
     class="fixed inset-0 z-50 flex items-start justify-center pt-10 px-4 pb-4"
     style="display: none;">

    <div x-show="open"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="open = false; document.body.classList.remove('overflow-hidden')"
         class="fixed inset-0 bg-gray-900/50"></div>

    <div x-show="open"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         class="relative bg-white rounded-xl shadow-2xl w-full {{ $maxWidthClass }} z-10 max-h-[90vh] flex flex-col">

        @if($title)
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 shrink-0">
            <h3 class="text-base font-semibold text-gray-800">{{ $title }}</h3>
            <button @click="open = false; document.body.classList.remove('overflow-hidden')"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        @endif

        <div class="p-5 overflow-y-auto">
            {{ $slot }}
        </div>
    </div>
</div>
