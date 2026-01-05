@component('mail::message')
## Seu carrinho está te esperando

Olá {{ $user->name }},

Você deixou alguns livros no carrinho. Que tal finalizar sua compra agora?

Sugestão do momento: **{{ $book->title }}**

Acesse o PedBook para revisar seu carrinho e concluir em poucos cliques:

@component('mail::button', ['url' => config('app.url')])
Ver meu carrinho
@endcomponent

{{ config('app.url') }}
@endcomponent
