@php
$navItems = [
    ['route' => 'configuracoes.petshop',     'label' => 'Petshop'],
    ['route' => 'configuracoes.servicos',    'label' => 'Serviços'],
    ['route' => 'configuracoes.colaboradores','label' => 'Colaboradores'],
    ['route' => 'configuracoes.horarios',    'label' => 'Horários'],
    ['route' => 'configuracoes.fidelidade',  'label' => 'Fidelidade'],
    ['route' => 'configuracoes.integracao',  'label' => 'Integração'],
];
@endphp
<div class="flex gap-1 flex-wrap border-b border-gray-200 dark:border-gray-700 pb-3 mb-2">
    @foreach($navItems as $item)
        <a href="{{ route($item['route']) }}"
           class="px-3 py-1.5 text-sm rounded-lg font-medium transition-colors {{ request()->routeIs($item['route']) ? 'bg-brand text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            {{ $item['label'] }}
        </a>
    @endforeach
</div>
