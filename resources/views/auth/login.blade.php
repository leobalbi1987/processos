@php
    // Tradução manual simples caso o arquivo de tradução não esteja carregado
    $lang = [
        'email' => 'E-mail',
        'password' => 'Senha',
        'remember_me' => 'Lembrar de mim',
        'forgot_password' => 'Esqueceu a senha?',
        'login' => 'Entrar'
    ];
@endphp

<x-guest-layout>
    <!-- Status da sessão -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="$lang['email']" class="text-blue-100" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Senha -->
        <div class="mt-4">
            <x-input-label for="password" :value="$lang['password']" class="text-blue-100" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Lembrar -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-600 bg-gray-900/50 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                <span class="ms-2 text-sm text-gray-300">{{ $lang['remember_me'] }}</span>
            </label>
        </div>

        <!-- Ações -->
        <div class="mt-8 flex flex-col gap-4">
            <button type="submit" 
                class="w-full text-white font-bold py-4 px-6 rounded-lg shadow-2xl transition duration-300 transform hover:scale-[1.02] uppercase tracking-widest text-sm"
                style="background-color: #0056b3 !important; display: block !important; visibility: visible !important; opacity: 1 !important;">
                {{ $lang['login'] }}
            </button>

            @if (Route::has('password.request'))
                <div class="text-center">
                    <a class="underline text-sm text-blue-300 hover:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" href="{{ route('password.request') }}">
                        {{ $lang['forgot_password'] }}
                    </a>
                </div>
            @endif
        </div>
    </form>
</x-guest-layout>
