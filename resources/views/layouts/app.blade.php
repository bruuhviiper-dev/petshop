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
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-950 min-h-screen transition-colors duration-200"
      x-data="{ sidebarOpen: false, dark: localStorage.getItem('darkMode') === 'true',
                toggleDark() { this.dark = !this.dark; localStorage.setItem('darkMode', this.dark); document.documentElement.classList.toggle('dark', this.dark); } }">

    {{-- Backdrop (apenas mobile, quando a sidebar está aberta) --}}
    <div x-show="sidebarOpen" x-cloak
         x-transition:enter="transition-opacity ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-gray-900/50 z-30 lg:hidden"></div>

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 h-full w-60 bg-gray-900 dark:bg-gray-950 text-white flex flex-col z-40 shadow-xl transition-transform duration-200 lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        <div class="p-4 border-b border-gray-700 flex items-center justify-between">
            @if(isset($currentPetshop) && $currentPetshop->logo)
                <img src="{{ Storage::url($currentPetshop->logo) }}" alt="{{ $currentPetshop->name }}" class="h-10 w-auto">
            @else
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-brand flex items-center justify-center text-white font-bold text-sm shrink-0">
                        {{ substr($currentPetshop->name ?? 'P', 0, 1) }}
                    </div>
                    <span class="font-semibold text-sm truncate">{{ $currentPetshop->name ?? config('app.name') }}</span>
                </div>
            @endif
            {{-- Fechar (apenas mobile) --}}
            <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-white" aria-label="Fechar menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <nav class="flex-1 p-3 space-y-0.5 overflow-y-auto">
            @php
            $links = [
                ['route' => 'dashboard',            'label' => 'Dashboard',    'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                ['route' => 'agenda.index',         'label' => 'Agenda',       'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ['route' => 'pdv.index',            'label' => 'PDV / Vendas', 'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z'],
                ['route' => 'produtos.index',       'label' => 'Produtos',     'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
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
            <a href="{{ route('profile.edit') }}"
               {{ request()->routeIs('profile.*') ? 'aria-current=page' : '' }}
               class="flex items-center gap-3 rounded-lg p-2 -m-1 mb-1 transition-colors {{ request()->routeIs('profile.*') ? 'bg-gray-800' : 'hover:bg-gray-800' }}">
                <x-avatar :name="Auth::user()->name" />
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400 capitalize">{{ Auth::user()->role }} · editar perfil</p>
                </div>
                <svg class="w-4 h-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-2 py-2 rounded-lg text-sm text-gray-400 hover:text-white hover:bg-gray-800 transition-colors" title="Sair" aria-label="Sair do sistema">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Sair
                </button>
            </form>
        </div>
    </aside>

    {{-- Main content area --}}
    <div class="lg:ml-60 min-h-screen flex flex-col">

        <header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 px-4 sm:px-6 py-3 flex items-center justify-between sticky top-0 z-20">
            <div class="flex items-center gap-3">
                {{-- Abrir menu (apenas mobile) --}}
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200" aria-label="Abrir menu lateral">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="text-base font-semibold text-gray-800 dark:text-gray-100">{{ $title ?? 'PetAgenda' }}</h1>
            </div>
            <div class="flex items-center gap-3">
                {{-- Dark / light mode: ícones lado a lado --}}
                <div class="inline-flex items-center gap-0.5 p-0.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    <button @click="if(dark) toggleDark()" type="button"
                            :class="!dark ? 'bg-white text-amber-500 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                            class="p-1.5 rounded-md transition-colors" aria-label="Modo claro">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </button>
                    <button @click="if(!dark) toggleDark()" type="button"
                            :class="dark ? 'bg-gray-700 text-violet-300 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                            class="p-1.5 rounded-md transition-colors" aria-label="Modo escuro">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </button>
                </div>
                @if(isset($currentPetshop))
                    <a href="{{ route('publico.agendar', $currentPetshop->slug) }}" target="_blank"
                       class="hidden sm:flex text-xs text-gray-500 dark:text-gray-400 hover:text-brand transition-colors items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Link público
                    </a>
                @endif
            </div>
        </header>

        <main class="p-4 sm:p-6 flex-1">
            <x-flash />
            {{ $slot }}
        </main>
    </div>
</body>
</html>
