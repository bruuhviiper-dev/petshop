<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carteirinha de {{ $pet->name }} — {{ $petshop->name }}</title>
    @vite(['resources/css/app.css'])
    <style>
        :root { --color-brand: {{ $petshop->primary_color ?? '#8B5CF6' }}; }
        .bg-brand { background-color: var(--color-brand); }
        .text-brand { color: var(--color-brand); }
        .border-brand { border-color: var(--color-brand); }
        @media print { .no-print { display: none !important; } body { background: #fff !important; } }
    </style>
</head>
<body class="bg-gray-100 min-h-screen font-sans antialiased py-6 px-4">

    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">

            {{-- Cabeçalho com a marca --}}
            <div class="bg-brand px-5 py-4 flex items-center gap-3 text-white">
                @if($petshop->logo)
                    <img src="{{ Storage::url($petshop->logo) }}" alt="{{ $petshop->name }}" class="h-10 w-auto rounded">
                @else
                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center font-bold">{{ substr($petshop->name, 0, 1) }}</div>
                @endif
                <div class="leading-tight">
                    <p class="font-bold">{{ $petshop->name }}</p>
                    <p class="text-xs text-white/80">Carteirinha digital do pet</p>
                </div>
            </div>

            {{-- Identificação --}}
            <div class="px-5 pt-5 flex items-center gap-4">
                @if($pet->photo)
                    <img src="{{ Storage::url($pet->photo) }}" alt="{{ $pet->name }}" class="w-20 h-20 rounded-xl object-cover ring-2 ring-brand">
                @else
                    <div class="w-20 h-20 rounded-xl bg-gray-100 flex items-center justify-center text-4xl">🐾</div>
                @endif
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-gray-800 truncate">{{ $pet->name }}</h1>
                    <p class="text-sm text-gray-500">{{ $pet->species }}@if($pet->breed) · {{ $pet->breed }}@endif</p>
                    @if($pet->weight)<p class="text-xs text-gray-400">{{ $pet->weight }} kg @if($pet->temperament)· {{ $pet->temperament }}@endif</p>@endif
                </div>
            </div>

            {{-- Alergias / alerta --}}
            @if($pet->allergies)
                <div class="mx-5 mt-4 p-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
                    <span class="font-semibold">⚠️ Alergias / cuidados:</span> {{ $pet->allergies }}
                </div>
            @endif

            {{-- Contatos de emergência --}}
            <div class="px-5 mt-4 grid grid-cols-2 gap-3 text-sm">
                <div class="p-3 rounded-lg bg-gray-50">
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Tutor</p>
                    <p class="font-medium text-gray-800 truncate">{{ $pet->cliente->name }}</p>
                    @if($pet->cliente->phone)<a href="tel:{{ $pet->cliente->phone }}" class="text-brand text-xs">{{ $pet->cliente->phone }}</a>@endif
                </div>
                <div class="p-3 rounded-lg bg-gray-50">
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Petshop</p>
                    <p class="font-medium text-gray-800 truncate">{{ $petshop->name }}</p>
                    @if($petshop->phone)<a href="tel:{{ $petshop->phone }}" class="text-brand text-xs">{{ $petshop->phone }}</a>@endif
                </div>
            </div>

            {{-- Vacinas --}}
            <div class="px-5 mt-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Carteira de vacinação</p>
                @forelse($pet->vacinas as $v)
                    <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0 text-sm">
                        <div>
                            <p class="font-medium text-gray-800">{{ $v->name }}</p>
                            <p class="text-xs text-gray-400">Aplicada em {{ optional($v->date_applied)->format('d/m/Y') ?? '—' }}</p>
                        </div>
                        @if($v->next_date)
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $v->next_date->isPast() ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                Próxima: {{ $v->next_date->format('d/m/Y') }}
                            </span>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Nenhuma vacina registrada.</p>
                @endforelse
            </div>

            {{-- QR Code --}}
            <div class="px-5 py-5 mt-3 border-t border-gray-100 flex items-center gap-4">
                <div class="shrink-0 [&>svg]:w-24 [&>svg]:h-24">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(96)->margin(0)->generate(route('pet.carteirinha', $pet->public_token)) !!}
                </div>
                <div class="text-xs text-gray-500">
                    <p class="font-semibold text-gray-700">Carteirinha digital</p>
                    <p>Escaneie para abrir a ficha do pet no celular. Apresente na recepção no check-in.</p>
                </div>
            </div>
        </div>

        <button onclick="window.print()" class="no-print mt-4 w-full py-3 bg-brand text-white text-sm font-semibold rounded-xl hover:opacity-90 transition-opacity">
            Imprimir / salvar em PDF
        </button>
        <p class="no-print text-center text-xs text-gray-400 mt-3">Powered by {{ config('app.name', 'PetAgenda') }}</p>
    </div>

</body>
</html>
