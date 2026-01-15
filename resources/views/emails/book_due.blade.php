@component('mail::message')
## Lembrete de devolução

Olá {{ $user->name }},

Este é um lembrete de que o livro **{{ $book->title }}** está previsto para devolução em **{{ optional($loan->return_date)->format('d/m/Y') }}**.

@php
    $daysAhead = null;
    $dueMessage = null;

    if ($loan->return_date) {
        $daysAhead = (int) now()->startOfDay()->diffInDays($loan->return_date->startOfDay(), false);

        if ($daysAhead >= 0) {
            if ($daysAhead === 0) {
                $dueMessage = 'O prazo de devolução é hoje.';
            } else {
                $dueMessage = 'Faltam ' . $daysAhead . ' ' . ($daysAhead === 1 ? 'dia' : 'dias') . ' para a data de devolução.';
            }
        }
    }
@endphp

@if ($dueMessage)
@component('mail::panel')
{{ $dueMessage }}
@endcomponent
@endif

Acesse o PedBook para ver seus empréstimos e gerenciar suas devoluções:

@component('mail::button', ['url' => config('app.url')])
Ver meus empréstimos
@endcomponent

{{ config('app.url') }}
@endcomponent
