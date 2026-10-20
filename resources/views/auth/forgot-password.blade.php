<x-guest-layout>
    <div class="min-h-screen flex justify-center items-center bg-gray-500">
        <!-- Contenedor con fondo gris -->
        <div class="w-full sm:max-w-md p-6 bg-gray-700 rounded-lg shadow-md text-center opacity-0 animate-fadeIn">
            <!-- Contenedor con recuadro blanco -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <!-- Título y descripción -->
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                    {{ __('Restablece tu contraseña') }}
                </h2>
                <p class="text-sm text-gray-600 mb-6">
                    {{ __('¿Olvidaste tu contraseña? No te preocupes. Ingresa tu correo electrónico y te enviaremos un enlace para restablecerla.') }}
                </p>

                <!-- Estado de la sesión -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <!-- Formulario -->
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <!-- Dirección de correo electrónico -->
                    <div class="mb-4">
                        <x-input-label for="email" :value="__('Correo electrónico')" class="text-black-800" />
                        <x-text-input id="email" class="block mt-1 w-full rounded-md border-gray-600 bg-gray-100 text-gray-800 shadow-sm focus:ring-gray-500 focus:border-gray-500" type="email" name="email" :value="old('email')" required autofocus />
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400" />
                    </div>

                    <!-- Botón de enviar -->
                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button class="w-full bg-gray-600 hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50">
                            {{ __('Enviar enlace de restablecimiento de contraseña') }}
                        </x-primary-button>
                    </div>
                </form>

                <!-- Enlace para ir al login -->
                <div class="mt-4">
                    <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-500">
                        {{ __('¿Ya tienes cuenta? Iniciar sesión') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Animaciones -->
    <style>
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        .animate-fadeIn {
            animation: fadeIn 0.8s ease-out forwards;
        }
    </style>
</x-guest-layout>
