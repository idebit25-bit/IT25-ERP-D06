<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-900">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-800">
            <div class="mb-4">
                
                
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-gray-700 dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
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
                            <x-text-input id="email" class="block mt-1 w-full rounded-md border-black-600 bg-black-100 text-gray-800 shadow-sm focus:ring-gray-500 focus:border-gray-500" type="email" name="email" :value="old('email')" required autofocus />
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
    </body>
</html>
