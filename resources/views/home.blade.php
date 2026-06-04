<x-slot:title>PetAgenda — Sistema Premium para Petshops</x-slot:title>

<x-landing-layout>

{{-- ═══════════════════ NAVBAR ═══════════════════ --}}
<nav class="fixed top-0 inset-x-0 z-50 transition-all duration-300"
     x-data="{ scrolled: false }"
     x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 20)"
     :class="scrolled ? 'bg-white/90 dark:bg-gray-950/90 backdrop-blur shadow-lg shadow-black/5' : 'bg-transparent'">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 lg:h-18">

            {{-- Logo --}}
            <a href="/" class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg gradient-brand flex items-center justify-center shadow-md">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <span class="font-bold text-lg text-gray-900 dark:text-white">Pet<span class="text-violet-600 dark:text-violet-400">Agenda</span></span>
            </a>

            {{-- Desktop Nav --}}
            <div class="hidden md:flex items-center gap-1">
                @foreach ([['#features','Funcionalidades'],['#stack','Stack'],['#preview','Preview'],['#pricing','Preço']] as [$href,$label])
                    <a href="{{ $href }}"
                       class="px-3 py-1.5 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-violet-600 dark:hover:text-violet-400 hover:bg-violet-50 dark:hover:bg-violet-900/20 transition-colors">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- CTA + Dark mode --}}
            <div class="flex items-center gap-3">
                <button @click="dark = !dark; localStorage.setItem('darkMode', dark)"
                        class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        aria-label="Alternar modo escuro">
                    <svg x-show="!dark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg x-show="dark" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </button>
                <a href="{{ route('login') }}"
                   class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-violet-400 hover:text-violet-600 transition-colors">
                    Entrar
                </a>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white gradient-brand hover:opacity-90 transition-opacity shadow-md shadow-violet-500/20">
                    Acessar demo
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>

                {{-- Mobile menu button --}}
                <button @click="menuOpen = !menuOpen" class="md:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-show="menuOpen" x-cloak x-transition
             class="md:hidden pb-4 border-t border-gray-100 dark:border-gray-800">
            <div class="flex flex-col gap-1 pt-3">
                @foreach ([['#features','Funcionalidades'],['#stack','Stack'],['#preview','Preview'],['#pricing','Preço']] as [$href,$label])
                    <a href="{{ $href }}" @click="menuOpen = false"
                       class="px-3 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-violet-50 dark:hover:bg-violet-900/20 hover:text-violet-600 transition-colors">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</nav>

{{-- ═══════════════════ HERO ═══════════════════ --}}
<section class="gradient-hero min-h-screen flex flex-col items-center justify-center relative overflow-hidden px-4">

    {{-- Background blobs --}}
    <div class="absolute top-20 left-1/4 w-96 h-96 bg-violet-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-20 right-1/4 w-80 h-80 bg-purple-500/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-violet-800/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 max-w-5xl mx-auto text-center">

        {{-- Badge --}}
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full card-glass text-violet-300 text-xs font-medium mb-8 border border-violet-500/20">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
            Template Premium · Laravel 12 · Pronto para produção
        </div>

        {{-- Headline --}}
        <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold text-white leading-tight mb-6 tracking-tight">
            Gestão completa para<br>
            <span class="gradient-text">petshops modernos</span>
        </h1>

        <p class="text-lg sm:text-xl text-gray-300/80 max-w-2xl mx-auto mb-10 leading-relaxed">
            Sistema completo de agenda, clientes, financeiro e WhatsApp.
            Pronto para usar, fácil de personalizar, feito para vender.
        </p>

        {{-- CTAs --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-16">
            <a href="{{ route('login') }}"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl font-semibold text-white gradient-brand hover:opacity-90 transition-all shadow-xl shadow-violet-500/30 glow-purple text-base">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Acessar demo grátis
            </a>
            <a href="{{ route('publico.agendar', 'pet-tosa-ana') }}"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl font-semibold text-white card-glass hover:bg-white/10 transition-all border border-white/10 text-base">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Ver agendamento público
            </a>
        </div>

        {{-- Credenciais demo --}}
        <p class="text-gray-500 text-sm">
            Demo: <span class="font-mono text-violet-400">admin@demo.com</span> · <span class="font-mono text-violet-400">password</span>
        </p>
    </div>

    {{-- App preview mockup --}}
    <div class="relative z-10 w-full max-w-5xl mx-auto mt-16 px-2">
        <div class="rounded-2xl overflow-hidden shadow-2xl shadow-black/60 border border-white/10 glow-purple">
            {{-- Fake browser chrome --}}
            <div class="bg-gray-800 px-4 py-2.5 flex items-center gap-2">
                <div class="flex gap-1.5">
                    <div class="w-3 h-3 rounded-full bg-red-500/80"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-500/80"></div>
                    <div class="w-3 h-3 rounded-full bg-green-500/80"></div>
                </div>
                <div class="flex-1 bg-gray-700/60 rounded-md px-3 py-1 text-xs text-gray-400 text-center max-w-xs mx-auto">
                    localhost:8000/dashboard
                </div>
            </div>
            {{-- Dashboard screenshot simulation --}}
            <div class="bg-gray-950 flex" style="min-height: 400px;">
                {{-- Fake sidebar --}}
                <div class="w-52 bg-gray-900 border-r border-gray-800 p-3 flex flex-col gap-1 shrink-0 hidden sm:flex">
                    <div class="flex items-center gap-2 px-2 py-2.5 mb-2">
                        <div class="w-7 h-7 rounded-lg bg-violet-600 flex items-center justify-center text-white text-xs font-bold shrink-0">P</div>
                        <div>
                            <div class="text-white text-xs font-semibold">Pet & Tosa da Ana</div>
                        </div>
                    </div>
                    @foreach([
                        ['Dashboard','M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', true],
                        ['Agenda','M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', false],
                        ['Clientes','M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', false],
                        ['Financeiro','M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', false],
                    ] as [$name, $icon, $active])
                    <div class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs {{ $active ? 'bg-violet-600 text-white' : 'text-gray-400' }}">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                        </svg>
                        {{ $name }}
                    </div>
                    @endforeach
                </div>
                {{-- Fake content --}}
                <div class="flex-1 p-5 overflow-hidden">
                    <div class="text-gray-400 text-xs mb-4">Dashboard</div>
                    {{-- Metric cards --}}
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
                        @foreach([['Agendamentos Hoje','12','text-violet-400'],['Faturamento Mês','R$ 4.820','text-emerald-400'],['Taxa Ocupação','78%','text-amber-400'],['Clientes Ativos','47','text-blue-400']] as [$label,$value,$color])
                        <div class="bg-gray-800/60 rounded-xl p-3 border border-gray-700/50">
                            <div class="text-gray-500 text-[10px] uppercase tracking-wider mb-1">{{ $label }}</div>
                            <div class="font-bold text-base {{ $color }}">{{ $value }}</div>
                        </div>
                        @endforeach
                    </div>
                    {{-- Chart placeholder --}}
                    <div class="bg-gray-800/40 rounded-xl p-4 border border-gray-700/40 mb-3">
                        <div class="text-gray-500 text-[10px] mb-3 uppercase tracking-wider">Faturamento — últimos 30 dias</div>
                        <div class="flex items-end gap-1 h-20">
                            @foreach([30,45,25,60,80,55,70,45,90,75,85,60,95,70,80,65,100,85,75,90,70,85,95,80,70,88,92,78,85,95] as $h)
                            <div class="flex-1 rounded-sm bg-violet-600/40 hover:bg-violet-500/60 transition-colors"
                                 style="height: {{ $h }}%"></div>
                            @endforeach
                        </div>
                    </div>
                    {{-- Appointments list --}}
                    <div class="space-y-2">
                        @foreach([['Thor','Banho P','09:00','Confirmado','blue'],['Luna','Tosa','10:30','Em Andamento','purple'],['Mimi','Banho G','14:00','Pendente','amber']] as [$pet,$svc,$time,$status,$color])
                        <div class="flex items-center gap-3 bg-gray-800/40 rounded-lg px-3 py-2 border border-gray-700/30">
                            <span class="text-gray-400 font-mono text-[10px] shrink-0">{{ $time }}</span>
                            <div class="flex-1 min-w-0">
                                <span class="text-white text-xs font-medium">{{ $pet }}</span>
                                <span class="text-gray-500 text-[10px] ml-1">· {{ $svc }}</span>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium
                                {{ $color === 'blue' ? 'bg-blue-900/40 text-blue-400' : ($color === 'purple' ? 'bg-purple-900/40 text-purple-400' : 'bg-amber-900/40 text-amber-400') }}">
                                {{ $status }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scroll indicator --}}
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 text-gray-500 animate-bounce">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </div>
</section>

{{-- ═══════════════════ STATS ═══════════════════ --}}
<section class="py-14 bg-gray-50 dark:bg-gray-900 border-y border-gray-100 dark:border-gray-800">
    <div class="max-w-5xl mx-auto px-4 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
        @foreach([
            ['16+', 'Módulos completos'],
            ['Laravel 12', 'Stack moderno'],
            ['100%', 'Código aberto'],
            ['v2.0', 'Premium release'],
        ] as [$num, $label])
        <div>
            <div class="text-2xl sm:text-3xl font-bold text-violet-600 dark:text-violet-400 mb-1">{{ $num }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $label }}</div>
        </div>
        @endforeach
    </div>
</section>

{{-- ═══════════════════ FEATURES ═══════════════════ --}}
<section id="features" class="py-24 px-4 bg-white dark:bg-gray-950">
    <div class="max-w-6xl mx-auto">

        <div class="text-center mb-16">
            <span class="inline-block px-3 py-1 rounded-full bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-400 text-xs font-semibold uppercase tracking-widest mb-4">
                Funcionalidades
            </span>
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Tudo que um petshop precisa
            </h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-xl mx-auto">
                De agendamentos a relatórios financeiros, o PetAgenda cobre cada aspecto da gestão do seu petshop.
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                [
                    'Agenda Visual',
                    'Grade horária por colaborador com arrastar e soltar. Cards coloridos por status, navegação por data e modal de novo agendamento com busca de cliente.',
                    'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                    'violet',
                ],
                [
                    'Link Público de Agendamento',
                    'Página personalizada com a cor e logo do petshop. Formulário em 3 etapas no celular, sem necessidade de login. Cria cliente e pet automaticamente.',
                    'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1',
                    'blue',
                ],
                [
                    'WhatsApp Automático',
                    'Confirmação, lembrete 24h, lembrete 1h antes, avaliação pós-atendimento e lembrete de retorno. Todos via jobs com retry automático.',
                    'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
                    'emerald',
                ],
                [
                    'Financeiro Completo',
                    'Receitas, despesas, lucro líquido e ticket médio. Exportação CSV com filtros. Comissões calculadas automaticamente por colaborador.',
                    'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    'amber',
                ],
                [
                    'Ficha Completa do Pet',
                    'Histórico de agendamentos, vacinas, peso, temperamento, alergias e foto. Programa de fidelidade configur ável com premiação automática.',
                    'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                    'pink',
                ],
                [
                    'Dashboard com Gráficos',
                    'Métricas do dia, faturamento dos últimos 30 dias, top clientes e serviços. Alertas de pets com retorno atrasado. Gráficos Chart.js.',
                    'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                    'cyan',
                ],
            ] as [$title, $desc, $icon, $color])
            @php
            $colorMap = [
                'violet' => ['bg' => 'bg-violet-100 dark:bg-violet-900/30', 'text' => 'text-violet-600 dark:text-violet-400'],
                'blue'   => ['bg' => 'bg-blue-100 dark:bg-blue-900/30',   'text' => 'text-blue-600 dark:text-blue-400'],
                'emerald'=> ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30','text'=>'text-emerald-600 dark:text-emerald-400'],
                'amber'  => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-600 dark:text-amber-400'],
                'pink'   => ['bg' => 'bg-pink-100 dark:bg-pink-900/30',   'text' => 'text-pink-600 dark:text-pink-400'],
                'cyan'   => ['bg' => 'bg-cyan-100 dark:bg-cyan-900/30',   'text' => 'text-cyan-600 dark:text-cyan-400'],
            ];
            $c = $colorMap[$color];
            @endphp
            <div class="feature-card group p-6 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 hover:border-violet-200 dark:hover:border-violet-800 hover:shadow-xl hover:shadow-violet-500/5">
                <div class="w-11 h-11 rounded-xl {{ $c['bg'] }} flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 {{ $c['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $title }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════ AGENDA PREVIEW ═══════════════════ --}}
<section id="preview" class="py-24 bg-gray-50 dark:bg-gray-900 px-4 overflow-hidden">
    <div class="max-w-6xl mx-auto">
        <div class="grid lg:grid-cols-2 gap-12 items-center">

            <div>
                <span class="inline-block px-3 py-1 rounded-full bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-400 text-xs font-semibold uppercase tracking-widest mb-4">
                    Interface
                </span>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-5">
                    Agenda visual que<br>sua equipe vai amar
                </h2>
                <p class="text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">
                    Grade horária com colunas por colaborador, cards coloridos por status, widget de próximos agendamentos e modal de criação sem sair da tela.
                </p>
                <ul class="space-y-3">
                    @foreach([
                        'Grade CSS grid de 7h às 20h em intervalos de 30min',
                        'Cards arrastáveis com atualização em tempo real',
                        'Busca de cliente + pet por nome ou telefone',
                        'Navegação por data sem reload de página',
                        'Dark mode completo com toggle persistido',
                    ] as $item)
                    <li class="flex items-start gap-3 text-sm text-gray-600 dark:text-gray-300">
                        <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
                <div class="mt-8">
                    <a href="{{ route('agenda.index') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white gradient-brand hover:opacity-90 transition-opacity shadow-lg shadow-violet-500/25">
                        Ver agenda ao vivo
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Agenda mockup --}}
            <div class="relative">
                <div class="rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-2xl">
                    <div class="bg-white dark:bg-gray-800 p-4">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <button class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-200">quinta-feira, 4 de junho</span>
                                <button class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                            <button class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-violet-600 text-white text-xs font-medium">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                Novo
                            </button>
                        </div>
                        {{-- Collab headers --}}
                        <div class="grid gap-2 mb-3" style="grid-template-columns: 48px 1fr 1fr">
                            <div></div>
                            @foreach([['MS','Marcos','Tosador'],['JF','Julia','Banhista']] as [$init,$name,$role])
                            <div class="text-center">
                                <div class="w-7 h-7 rounded-full bg-violet-600 text-white text-xs font-bold flex items-center justify-center mx-auto mb-1">{{ $init }}</div>
                                <div class="text-[10px] font-medium text-gray-700 dark:text-gray-200">{{ $name }}</div>
                                <div class="text-[10px] text-gray-400">{{ $role }}</div>
                            </div>
                            @endforeach
                        </div>
                        {{-- Time slots --}}
                        @foreach([
                            ['09:00', null, ['Thor','Banho P','blue']],
                            ['09:30', null, null],
                            ['10:00', ['Mimi','Tosa','purple'], null],
                            ['10:30', ['Rex','Banho+Tosa','gray'], null],
                            ['11:00', null, null],
                            ['11:30', null, null],
                            ['12:00', ['Pipoca','Tosa','green'], null],
                        ] as [$time, $col1, $col2])
                        <div class="grid gap-2 mb-px" style="grid-template-columns: 48px 1fr 1fr">
                            <div class="text-[10px] text-gray-400 font-mono pt-1.5 text-right pr-2">{{ $time }}</div>
                            <div class="min-h-[32px] rounded-lg {{ $col1 ? ($col1[2] === 'purple' ? 'bg-purple-100 dark:bg-purple-900/30 border border-purple-200 dark:border-purple-700' : ($col1[2] === 'green' ? 'bg-green-100 dark:bg-green-900/30 border border-green-200 dark:border-green-700' : 'bg-gray-100 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600')) : 'border border-dashed border-gray-100 dark:border-gray-700' }} px-2 py-1">
                                @if($col1)
                                    <div class="text-[10px] font-semibold {{ $col1[2] === 'purple' ? 'text-purple-700 dark:text-purple-300' : ($col1[2] === 'green' ? 'text-green-700 dark:text-green-300' : 'text-gray-500 dark:text-gray-400') }}">{{ $col1[0] }}</div>
                                    <div class="text-[9px] {{ $col1[2] === 'purple' ? 'text-purple-500' : ($col1[2] === 'green' ? 'text-green-500' : 'text-gray-400') }}">{{ $col1[1] }}</div>
                                @endif
                            </div>
                            <div class="min-h-[32px] rounded-lg {{ $col2 ? 'bg-blue-100 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700' : 'border border-dashed border-gray-100 dark:border-gray-700' }} px-2 py-1">
                                @if($col2)
                                    <div class="text-[10px] font-semibold text-blue-700 dark:text-blue-300">{{ $col2[0] }}</div>
                                    <div class="text-[9px] text-blue-500">{{ $col2[1] }}</div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                {{-- Floating badge --}}
                <div class="absolute -bottom-4 -right-4 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-3 flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-gray-800 dark:text-white">Concluído</div>
                        <div class="text-[10px] text-gray-400">Rex · Banho G</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════ STACK ═══════════════════ --}}
<section id="stack" class="py-20 px-4 bg-white dark:bg-gray-950">
    <div class="max-w-5xl mx-auto text-center">
        <span class="inline-block px-3 py-1 rounded-full bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-400 text-xs font-semibold uppercase tracking-widest mb-4">
            Stack
        </span>
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">Tecnologias modernas e consolidadas</h2>
        <p class="text-gray-500 dark:text-gray-400 mb-12">Sem frameworks JavaScript pesados. Blade puro, Alpine.js reativo e Tailwind CSS responsivo.</p>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach([
                ['Laravel 12', 'Backend robusto e elegante', '#FF2D20'],
                ['PHP 8.3', 'Readonly classes e typed properties', '#8892BF'],
                ['Tailwind CSS', 'Utilitários + dark mode nativo', '#06B6D4'],
                ['Alpine.js', 'Reatividade sem build step', '#77C1D2'],
                ['MySQL 8', 'Banco de dados confiável', '#4479A1'],
                ['Vite', 'Build ultrarrápido', '#646CFF'],
            ] as [$name, $desc, $color])
            <div class="p-5 rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 hover:border-gray-200 dark:hover:border-gray-700 transition-colors group">
                <div class="w-8 h-8 rounded-lg mx-auto mb-3 flex items-center justify-center"
                     style="background: {{ $color }}20;">
                    <div class="w-3 h-3 rounded-sm" style="background: {{ $color }};"></div>
                </div>
                <div class="text-sm font-semibold text-gray-800 dark:text-white mb-1">{{ $name }}</div>
                <div class="text-[11px] text-gray-400 leading-tight">{{ $desc }}</div>
            </div>
            @endforeach
        </div>

        {{-- Architecture pills --}}
        <div class="flex flex-wrap justify-center gap-2 mt-10">
            @foreach(['Services + Repositories','DTOs tipados','Form Requests','Policies','Events + Listeners','Jobs com retry','Queue database','Rate limiting','CSP headers','Docker Compose','GitHub Actions CI','Postman Collection'] as $pill)
            <span class="px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-xs border border-gray-200 dark:border-gray-700">
                {{ $pill }}
            </span>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════ PRICING ═══════════════════ --}}
<section id="pricing" class="py-24 px-4 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-4xl mx-auto text-center">
        <span class="inline-block px-3 py-1 rounded-full bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-400 text-xs font-semibold uppercase tracking-widest mb-4">
            Template Premium
        </span>
        <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-4">
            Um produto, infinitas instalações
        </h2>
        <p class="text-gray-500 dark:text-gray-400 mb-14 max-w-xl mx-auto">
            Compre uma vez e instale em quantos domínios quiser. Código-fonte completo, sem licença por instalação.
        </p>

        <div class="grid md:grid-cols-2 gap-6 max-w-3xl mx-auto">
            {{-- Free / Demo --}}
            <div class="p-8 rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-left">
                <div class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-2">Demo</div>
                <div class="text-4xl font-bold text-gray-900 dark:text-white mb-1">Grátis</div>
                <p class="text-sm text-gray-400 mb-6">Acesse o sistema completo com dados de demonstração.</p>
                <ul class="space-y-3 mb-8">
                    @foreach(['Sistema completo com dados demo','Todas as telas e funcionalidades','Sem cartão de crédito','Apenas para avaliação'] as $item)
                    <li class="flex items-center gap-2.5 text-sm text-gray-600 dark:text-gray-300">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('login') }}"
                   class="block w-full py-3 rounded-xl text-center text-sm font-semibold text-violet-700 dark:text-violet-400 border-2 border-violet-200 dark:border-violet-800 hover:bg-violet-50 dark:hover:bg-violet-900/20 transition-colors">
                    Acessar demo
                </a>
            </div>

            {{-- Premium --}}
            <div class="p-8 rounded-2xl bg-gray-900 dark:bg-violet-950 border border-gray-700 dark:border-violet-800 text-left relative overflow-hidden">
                <div class="absolute top-0 right-0 bg-violet-600 text-white text-[10px] font-bold px-3 py-1 rounded-bl-xl">
                    PREMIUM
                </div>
                <div class="text-sm font-semibold text-gray-400 mb-2">Código-fonte completo</div>
                <div class="text-4xl font-bold text-white mb-1">
                    Sob consulta
                </div>
                <p class="text-sm text-gray-400 mb-6">Licença para uso e revenda. Domínios ilimitados.</p>
                <ul class="space-y-3 mb-8">
                    @foreach([
                        'Código-fonte 100% documentado',
                        'Docker + Makefile + CI/CD pronto',
                        'Services, Repositories e DTOs',
                        'Dark mode + skeleton loaders',
                        'Rate limiting e CSP headers',
                        'Suporte via WhatsApp',
                        'Atualizações gratuitas',
                    ] as $item)
                    <li class="flex items-center gap-2.5 text-sm text-gray-300">
                        <svg class="w-4 h-4 text-violet-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
                <a href="https://wa.me/5511999990000?text=Olá! Tenho interesse no template PetAgenda Premium."
                   target="_blank"
                   class="block w-full py-3 rounded-xl text-center text-sm font-semibold text-white gradient-brand hover:opacity-90 transition-opacity shadow-lg shadow-violet-500/30">
                    Falar no WhatsApp
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════ CTA FINAL ═══════════════════ --}}
<section class="gradient-hero py-24 px-4 text-center relative overflow-hidden">
    <div class="absolute inset-0 opacity-30">
        <div class="absolute top-1/4 left-1/3 w-64 h-64 bg-violet-600 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/3 w-64 h-64 bg-purple-500 rounded-full blur-3xl"></div>
    </div>
    <div class="relative z-10 max-w-2xl mx-auto">
        <h2 class="text-3xl sm:text-4xl font-bold text-white mb-5">
            Pronto para ver o sistema em ação?
        </h2>
        <p class="text-gray-300 mb-10">
            Acesse agora com as credenciais de demo e explore todas as funcionalidades sem nenhum compromisso.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('login') }}"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl font-semibold text-white gradient-brand hover:opacity-90 transition-opacity shadow-xl shadow-violet-500/30 glow-purple">
                Acessar demo grátis
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
            <a href="{{ route('publico.agendar', 'pet-tosa-ana') }}"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl font-semibold text-white card-glass hover:bg-white/10 transition-colors border border-white/10">
                Link público de demo
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </a>
        </div>
        <p class="text-gray-500 text-xs mt-8">
            <span class="font-mono text-violet-400">admin@demo.com</span> · <span class="font-mono text-violet-400">password</span>
        </p>
    </div>
</section>

{{-- ═══════════════════ FOOTER ═══════════════════ --}}
<footer class="bg-gray-950 text-gray-500 py-10 px-4">
    <div class="max-w-5xl mx-auto">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-md gradient-brand flex items-center justify-center">
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <span class="text-gray-400 text-sm font-medium">PetAgenda</span>
                <span class="text-gray-700 text-xs">v2.0.0-premium</span>
            </div>
            <div class="flex items-center gap-6 text-xs">
                <a href="{{ route('login') }}" class="hover:text-gray-300 transition-colors">Sistema</a>
                <a href="{{ route('publico.agendar', 'pet-tosa-ana') }}" class="hover:text-gray-300 transition-colors">Demo público</a>
                <a href="https://github.com/bruuhviiper-dev/petshop" target="_blank" class="hover:text-gray-300 transition-colors">GitHub</a>
            </div>
            <div class="text-xs">
                Laravel 12 · PHP 8.3 · Tailwind CSS · Alpine.js
            </div>
        </div>
        <div class="border-t border-gray-800 mt-8 pt-6 text-center text-xs text-gray-700">
            Template premium para petshops. Uso e revenda permitidos. Domínios ilimitados.
        </div>
    </div>
</footer>

</x-landing-layout>
