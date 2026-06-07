<x-guest-layout>
    <x-slot:title>Confirmar senha — PetAgenda</x-slot:title>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Área protegida</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {{ __('Esta é uma área segura do sistema. Por favor, confirme sua senha para continuar.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Senha')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center py-3">
            {{ __('Confirmar') }}
        </x-primary-button>
    </form>
</x-guest-layout>
