<x-guest-layout>
    <x-slot:title>Verificar e-mail — PetAgenda</x-slot:title>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Verifique seu e-mail</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {{ __('Obrigado por se cadastrar! Antes de começar, verifique seu e-mail clicando no link que acabamos de enviar. Se não recebeu, podemos enviar outro.') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/50 px-4 py-3">
            <p class="text-sm text-emerald-700 dark:text-emerald-300">
                {{ __('Um novo link de verificação foi enviado para o e-mail informado no cadastro.') }}
            </p>
        </div>
    @endif

    <div class="flex items-center justify-between gap-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>
                {{ __('Reenviar e-mail') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                {{ __('Sair') }}
            </button>
        </form>
    </div>
</x-guest-layout>
