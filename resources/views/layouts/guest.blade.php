<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'PetAgenda') }}</title>

        {{-- Aplica dark mode ANTES do render para evitar flash --}}
        <script>if(localStorage.getItem('darkMode')==='true')document.documentElement.classList.add('dark');</script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:300,400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .gradient-brand {
                background: linear-gradient(135deg, #7C3AED 0%, #9333EA 50%, #6D28D9 100%);
            }
            .gradient-auth {
                background: linear-gradient(135deg, #0f0a1e 0%, #1e1040 45%, #2d1a5e 75%, #1a0f3a 100%);
            }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 transition-colors duration-300"
          x-data="{ dark: localStorage.getItem('darkMode') === 'true',
                    toggleDark() { this.dark = !this.dark; localStorage.setItem('darkMode', this.dark); document.documentElement.classList.toggle('dark', this.dark); } }">

        <div class="min-h-screen lg:grid lg:grid-cols-2">

            {{-- ── Painel lateral (somente desktop) ── --}}
            <div class="hidden lg:flex gradient-auth relative overflow-hidden flex-col justify-between p-12">
                {{-- Blobs decorativos --}}
                <div class="absolute top-10 -left-10 w-80 h-80 bg-violet-600/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute bottom-10 right-0 w-72 h-72 bg-purple-500/15 rounded-full blur-3xl pointer-events-none"></div>

                {{-- Logo --}}
                <a href="{{ url('/') }}" class="relative z-10 flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg gradient-brand flex items-center justify-center shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <span class="font-bold text-xl text-white">Pet<span class="text-violet-400">Agenda</span></span>
                </a>

                {{-- Texto central --}}
                <div class="relative z-10 max-w-md">
                    <h2 class="text-3xl font-bold text-white leading-tight mb-4">
                        Gestão completa para<br>petshops modernos
                    </h2>
                    <p class="text-gray-300/80 leading-relaxed mb-8">
                        Agenda, clientes, financeiro e WhatsApp em um só lugar. Acesse com as credenciais de demonstração e explore o sistema.
                    </p>
                    <ul class="space-y-3">
                        @foreach (['Agenda visual por colaborador', 'Link público de agendamento', 'Dashboard com gráficos e relatórios'] as $item)
                            <li class="flex items-center gap-3 text-sm text-gray-300">
                                <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ $item }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Credenciais demo --}}
                <div class="relative z-10 rounded-xl bg-white/5 backdrop-blur border border-white/10 p-4">
                    <div class="text-[11px] uppercase tracking-widest text-violet-300 font-semibold mb-2">Acesso de demonstração</div>
                    <div class="text-sm text-gray-300 font-mono">admin@demo.com</div>
                    <div class="text-sm text-gray-300 font-mono">password</div>
                </div>
            </div>

            {{-- ── Coluna do formulário ── --}}
            <div class="flex flex-col justify-center items-center px-4 py-10 sm:px-6 relative">

                {{-- Toggle dark / light mode: ícones lado a lado --}}
                <div class="absolute top-5 right-5 inline-flex items-center gap-0.5 p-0.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
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

                <div class="w-full max-w-md">
                    {{-- Logo mobile --}}
                    <a href="{{ url('/') }}" class="lg:hidden flex items-center justify-center gap-2.5 mb-8">
                        <div class="w-9 h-9 rounded-lg gradient-brand flex items-center justify-center shadow-md">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>
                        <span class="font-bold text-xl text-gray-900 dark:text-white">Pet<span class="text-violet-600 dark:text-violet-400">Agenda</span></span>
                    </a>

                    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl shadow-black/5 border border-gray-100 dark:border-gray-800 p-8">
                        {{ $slot }}
                    </div>

                    <p class="text-center text-xs text-gray-400 dark:text-gray-600 mt-6">
                        <a href="{{ url('/') }}" class="hover:text-violet-500 transition-colors">← Voltar para a página inicial</a>
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
