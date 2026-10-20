<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | Esta opción define el "guard" de autenticación predeterminado y el "broker"
    | de restablecimiento de contraseñas para tu aplicación. Puedes cambiar
    | estos valores según sea necesario, pero son una configuración ideal.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | A continuación, puedes definir cada guardia de autenticación para tu
    | aplicación. Se ha definido una configuración predeterminada excelente
    | que utiliza almacenamiento de sesiones y el proveedor Eloquent para usuarios.
    |
    | Soportados: "session"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | Todos los guards de autenticación tienen un proveedor de usuarios, el cual
    | define cómo se recuperan los usuarios de la base de datos o almacenamiento.
    |
    | Si tienes varias tablas o modelos de usuarios, puedes configurar varios
    | proveedores para representar el modelo o tabla. Estos proveedores se pueden
    | asignar a cualquier guard extra que hayas definido.
    |
    | Soportados: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', App\Models\User::class),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Restablecimiento de Contraseñas
    |--------------------------------------------------------------------------
    |
    | Estas opciones configuran el comportamiento de la funcionalidad de restablecimiento
    | de contraseñas de Laravel, incluyendo la tabla utilizada para el almacenamiento
    | de tokens y el proveedor de usuarios que se invoca para recuperar usuarios.
    |
    | El tiempo de expiración es el número de minutos que cada token de restablecimiento
    | se considera válido. Esta característica de seguridad mantiene los tokens de corta vida
    | para reducir el tiempo que pueden ser adivinados. Puedes cambiar esto según sea necesario.
    |
    | La configuración de "throttle" limita el número de segundos que un usuario debe esperar
    | antes de generar más tokens de restablecimiento de contraseña, para evitar que un usuario
    | genere rápidamente una gran cantidad de tokens.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Timeout de Confirmación de Contraseña
    |--------------------------------------------------------------------------
    |
    | Aquí puedes definir la cantidad de segundos antes de que venza la ventana
    | de confirmación de contraseña y los usuarios sean solicitados a ingresar
    | nuevamente su contraseña a través de la pantalla de confirmación.
    |
    | Por defecto, el tiempo de espera es de tres horas.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
