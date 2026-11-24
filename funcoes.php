<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function e(mixed $valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

function dinheiro(float|string $valor): string {
    return 'R$ ' . number_format((float)$valor, 2, ',', '.');
}

function redirecionar(string $pagina): never {
    header("Location: {$pagina}");
    exit;
}

function token_csrf(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function validar_csrf(): void {
    $token = $_POST['csrf'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf'] ?? '', $token)) {
        http_response_code(403);
        exit('Solicitação inválida. Volte à página e tente novamente.');
    }
}

function cabecalho(string $titulo): void {
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= e($titulo) ?> | Mercado Fácil</title>
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
    <header class="topo">
        <a class="marca" href="index.php">🛒 Mercado Fácil</a>
        <nav>
            <a href="index.php">Início</a>
            <a href="produtos.php">Produtos</a>
            <a href="cliente.php">Loja do cliente</a>
            <a href="vendas.php">Vendas</a>
            <a href="carrinho.php">Carrinho (<?= array_sum($_SESSION['carrinho'] ?? []) ?>)</a>
        </nav>
    </header>
    <main class="container">
    <?php
}

function rodape(): void {
    ?>
    </main>
    <footer class="rodape">Projeto acadêmico — Sistema de Supermercado</footer>
    </body>
    </html>
    <?php
}
