@props(['title' => null, 'class' => ''])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 dark:border-gray-700 rounded-xl shadow-sm border border-gray-100 ' . $class]) }}>
    @if($title)
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $title }}</h3>
        </div>
    @endif
    <div class="p-5">
        {{ $slot }}
    </div>
</div>
