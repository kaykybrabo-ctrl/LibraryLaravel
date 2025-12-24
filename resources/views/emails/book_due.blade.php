<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lembrete de devolução de livro</title>
</head>
<body>
    <p>Olá {{ $user->name }},</p>
    <p>Este é um lembrete de que o livro <strong>{{ $book->title }}</strong> está previsto para devolução em {{ optional($loan->return_date)->format('d/m/Y') }}.</p>
    <p>Acesse o PedBook para ver seus empréstimos e gerenciar suas devoluções.</p>
</body>
</html>
