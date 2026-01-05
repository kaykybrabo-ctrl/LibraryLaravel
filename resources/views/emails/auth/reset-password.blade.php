@component('mail::message')
# Redefinição de senha

Olá {{ $user->name }},

Recebemos uma solicitação para redefinir a sua senha no **PedBook**.

@component('mail::button', ['url' => $url])
Redefinir senha
@endcomponent

Se você não solicitou esta alteração, você pode ignorar este e-mail com segurança.

Obrigado,
{{ config('app.name', 'PedBook') }}
@endcomponent
