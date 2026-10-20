@component('mail::message')
# Restablecimiento de Contraseña

Hemos recibido una solicitud para restablecer la contraseña de tu cuenta. 

Haz clic en el botón de abajo para continuar.

@component('mail::button', ['url' => route('password.reset', ['token' => $token])])
Restablecer Contraseña
@endcomponent

Si no solicitaste un cambio de contraseña, ignora este correo.

Gracias,  
{{ config('app.name') }}
@endcomponent
