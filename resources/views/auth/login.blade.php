<x-guest-layout>
    <x-slot:title>Entrar — PetAgenda</x-slot:title>

    {{-- Cabeçalho --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Bem-vindo de volta</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Entre com suas credenciais para acessar o painel.</p>
    </div>

    {{-- Dica de credenciais demo (apenas em ambiente local) --}}
    @if (app()->environment('local'))
        <div class="mb-6 rounded-xl bg-violet-50 dark:bg-violet-900/20 border border-violet-100 dark:border-violet-800/50 px-4 py-3">
            <p class="text-xs text-violet-700 dark:text-violet-300">
                <span class="font-semibold">Demo:</span>
                <span class="font-mono">admin@demo.com</span> ·
                <span class="font-mono">password</span>
            </p>
        </div>
    @endif

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('E-mail')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="voce@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Senha')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me + Forgot -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-violet-600 shadow-sm focus:ring-violet-500" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Lembrar de mim') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-violet-600 dark:text-violet-400 hover:text-violet-700 dark:hover:text-violet-300 transition-colors" href="{{ route('password.request') }}">
                    {{ __('Esqueceu a senha?') }}
                </a>
            @endif
        </div>

        <x-primary-button class="w-full justify-center py-3">
            {{ __('Entrar') }}
        </x-primary-button>

        @if (Route::has('register'))
            <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                Não tem uma conta?
                <a href="{{ route('register') }}" class="font-semibold text-violet-600 dark:text-violet-400 hover:text-violet-700 dark:hover:text-violet-300 transition-colors">Criar conta</a>
            </p>
        @endif
    </form>
</x-guest-layout>
