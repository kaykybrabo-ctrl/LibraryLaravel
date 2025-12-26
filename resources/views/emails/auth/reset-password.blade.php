@component('mail::message')
# Redefinir senha

Olá {{ $user->name }},

Você solicitou a redefinição da sua senha no **PedBook**.

@component('mail::button', ['url' => $url])
Redefinir senha
@endcomponent

Se você não solicitou esta redefinição, pode ignorar este e-mail.

Obrigado,  
{{ config('app.name', 'PedBook') }}
@endcomponent
