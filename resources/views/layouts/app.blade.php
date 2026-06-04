<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    {{-- Aplica dark mode ANTES do render para evitar flash --}}
    <script>if(localStorage.getItem('darkMode')==='true')document.documentElement.classList.add('dark');</script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <style>
        :root { --color-brand: {{ $currentPetshop->primary_color ?? '#3B82F6' }}; }
        .bg-brand { background-color: var(--color-brand) !important; }
        .text-brand { color: var(--color-brand) !important; }
        .border-brand { border-color: var(--color-brand) !important; }
        .hover\:bg-brand:hover { background-color: var(--color-brand) !important; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-950 min-h-screen transition-colors duration-200"
      x-data="{ sidebarOpen: window.innerWidth >= 1024, dark: localStorage.getItem('darkMode') === 'true',
                toggleDark() { this.dark = !this.dark; localStorage.setItem('darkMode', this.dark); document.documentElement.classList.toggle('dark', this.dark); } }">

    {{-- Sidebar --}}
    <aside class="fixed top-0 left-0 h-full w-60 bg-gray-900 dark:bg-gray-950 text-white flex flex-col z-30 shadow-xl transition-transform duration-200"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        <div class="p-4 border-b border-gray-700">
            @if(isset($currentPetshop) && $currentPetshop->logo)
                <img src="{{ Storage::url($currentPetshop->logo) }}" alt="{{ $currentPetshop->name }}" class="h-10 w-auto">
            @else
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-full bg-brand flex items-center justify-center text-white font-bold text-sm shrink-0">
                        {{ substr($currentPetshop->name ?? 'P', 0, 1) }}
                    </div>
                    <span class="font-semibold text-sm truncate">{{ $currentPetshop->name ?? config('app.name') }}</span>
                </div>
            @endif
        </div>

        <nav class="flex-1 p-3 space-y-0.5 overflow-y-auto">
            @php
            $links = [
                ['route' => 'dashboard',            'label' => 'Dashboard',    'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                ['route' => 'agenda.index',         'label' => 'Agenda',       'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ['route' => 'clientes.index',       'label' => 'Clientes',     'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ['route' => 'financeiro.index',     'label' => 'Financeiro',   'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['route' => 'comissoes.index',      'label' => 'Comissões',    'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z'],
            ];
            @endphp
            @foreach($links as $link)
                @php $isActive = request()->routeIs(explode('.', $link['route'])[0].'*'); @endphp
                <a href="{{ route($link['route']) }}"
                   {{ $isActive ? 'aria-current=page' : '' }}
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $isActive ? 'bg-brand text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"/>
                    </svg>
                    {{ $link['label'] }}
                </a>
            @endforeach

            <div class="pt-3 mt-3 border-t border-gray-700">
                @php $isConfigActive = request()->routeIs('configuracoes*'); @endphp
                <a href="{{ route('configuracoes.petshop') }}"
                   {{ $isConfigActive ? 'aria-current=page' : '' }}
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $isConfigActive ? 'bg-brand text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Configurações
                </a>
            </div>
        </nav>

        <div class="p-4 border-t border-gray-700">
            <div class="flex items-center gap-3">
                <x-avatar :name="Auth::user()->name" />
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400 capitalize">{{ Auth::user()->role }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-white transition-colors" title="Sair" aria-label="Sair do sistema">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main content area --}}
    <div class="transition-all duration-200" :class="sidebarOpen ? 'ml-60' : 'ml-0'">

        <header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 px-6 py-3 flex items-center justify-between sticky top-0 z-20">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200" aria-label="Alternar menu lateral">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="text-base font-semibold text-gray-800 dark:text-gray-100">{{ $title ?? 'PetAgenda' }}</h1>
            </div>
            <div class="flex items-center gap-3">
                {{-- Dark mode toggle --}}
                <button @click="toggleDark()" class="p-1.5 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" aria-label="Alternar modo escuro">
                    <svg x-show="!dark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg x-show="dark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </button>
                @if(isset($currentPetshop))
                    <a href="{{ route('publico.agendar', $currentPetshop->slug) }}" target="_blank"
                       class="text-xs text-gray-500 dark:text-gray-400 hover:text-brand transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Link público
                    </a>
                @endif
            </div>
        </header>

        <main class="p-6 min-h-screen">
            <x-flash />
            {{ $slot }}
        </main>
    </div>

    <script>
    function darkModeApp() {
        return {
            dark: localStorage.getItem('darkMode') === 'true',
            toggleDark() {
                this.dark = !this.dark;
                localStorage.setItem('darkMode', this.dark);
            },
        };
    }
    </script>
</body>
</html>
