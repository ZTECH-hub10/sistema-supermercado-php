<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$totalProdutos = (int)$pdo->query("SELECT COUNT(*) FROM produtos")->fetchColumn();
$estoqueBaixo = (int)$pdo->query("SELECT COUNT(*) FROM produtos WHERE quantidade <= 5")->fetchColumn();
$totalVendas = (int)$pdo->query("SELECT COUNT(*) FROM vendas")->fetchColumn();
$faturamento = (float)$pdo->query("SELECT COALESCE(SUM(total), 0) FROM vendas")->fetchColumn();

cabecalho('Painel administrativo');
?>
<section class="hero">
    <div>
        <p class="sobretitulo">PAINEL ADMINISTRATIVO</p>
        <h1>Bem-vindo ao Mercado Fácil</h1>
        <p>Gerencie produtos, acompanhe o estoque e consulte as vendas do supermercado.</p>
    </div>
    <a class="botao botao-claro" href="cliente.php">Abrir loja do cliente →</a>
</section>

<div class="cards">
    <article class="card-resumo"><span>Produtos cadastrados</span><strong><?= $totalProdutos ?></strong><a href="produtos.php">Gerenciar produtos →</a></article>
    <article class="card-resumo"><span>Produtos com estoque baixo</span><strong><?= $estoqueBaixo ?></strong><a href="produtos.php">Conferir estoque →</a></article>
    <article class="card-resumo"><span>Vendas realizadas</span><strong><?= $totalVendas ?></strong><a href="vendas.php">Ver vendas →</a></article>
    <article class="card-resumo"><span>Faturamento registrado</span><strong><?= dinheiro($faturamento) ?></strong><a href="vendas.php">Ver histórico →</a></article>
</div>

<section class="painel">
    <h2>Atalhos do sistema</h2>
    <div class="atalhos">
        <a class="atalho" href="produtos.php"><span>📦</span><b>Cadastro de produtos</b><small>Adicionar, editar e excluir produtos</small></a>
        <a class="atalho" href="cliente.php"><span>🛍️</span><b>Loja do cliente</b><small>Escolher produtos para comprar</small></a>
        <a class="atalho" href="carrinho.php"><span>🛒</span><b>Carrinho de compras</b><small>Conferir itens e finalizar pedido</small></a>
        <a class="atalho" href="vendas.php"><span>🧾</span><b>Histórico de vendas</b><small>Consultar compras realizadas</small></a>
    </div>
</section>
<?php rodape(); ?>
