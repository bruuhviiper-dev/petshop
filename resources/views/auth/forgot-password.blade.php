<x-guest-layout>
    <x-slot:title>Recuperar senha — PetAgenda</x-slot:title>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Recuperar senha</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {{ __('Esqueceu sua senha? Sem problemas. Informe seu e-mail e enviaremos um link para você definir uma nova senha.') }}
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('E-mail')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus placeholder="voce@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center py-3">
            {{ __('Enviar link de recuperação') }}
        </x-primary-button>

        <p class="text-center text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('login') }}" class="font-semibold text-violet-600 dark:text-violet-400 hover:text-violet-700 dark:hover:text-violet-300 transition-colors">← Voltar para o login</a>
        </p>
    </form>
</x-guest-layout>
