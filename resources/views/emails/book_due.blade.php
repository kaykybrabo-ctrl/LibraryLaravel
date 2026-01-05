@component('mail::message')
## Lembrete de devolução

Olá {{ $user->name }},

Este é um lembrete de que o livro **{{ $book->title }}** está previsto para devolução em **{{ optional($loan->return_date)->format('d/m/Y') }}**.

Acesse o PedBook para ver seus empréstimos e gerenciar suas devoluções:

@component('mail::button', ['url' => config('app.url')])
Ver meus empréstimos
@endcomponent

{{ config('app.url') }}
@endcomponent
