<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$produtos = $pdo->query("SELECT * FROM produtos WHERE quantidade > 0 ORDER BY nome")->fetchAll();
cabecalho('Loja do cliente');
?>
<section class="hero hero-cliente">
    <div><p class="sobretitulo">LOJA DO CLIENTE</p><h1>Encontre seus produtos</h1><p>Escolha os itens e adicione ao carrinho para simular sua compra.</p></div>
    <a class="botao botao-claro" href="carrinho.php">Ver carrinho →</a>
</section>
<?php if (isset($_GET['adicionado'])): ?><div class="alerta sucesso">Produto adicionado ao carrinho!</div><?php endif; ?>
<div class="grade-produtos">
<?php foreach ($produtos as $p): ?>
    <article class="produto-card">
        <div class="produto-icone">🛒</div>
        <div class="produto-info">
            <h3><?= e($p['nome']) ?></h3>
            <p><?= e($p['descricao'] ?: 'Produto disponível no supermercado') ?></p>
            <strong><?= dinheiro($p['preco']) ?></strong>
            <small><?= (int)$p['quantidade'] ?> unidade(s) em estoque</small>
            <form method="post" action="carrinho.php" class="adicionar">
                <input type="hidden" name="csrf" value="<?= e(token_csrf()) ?>">
                <input type="hidden" name="acao" value="adicionar">
                <input type="hidden" name="produto_id" value="<?= (int)$p['id'] ?>">
                <input type="number" name="quantidade" min="1" max="<?= (int)$p['quantidade'] ?>" value="1" aria-label="Quantidade">
                <button class="botao" type="submit">Adicionar</button>
            </form>
        </div>
    </article>
<?php endforeach; ?>
</div>
<?php if (!$produtos): ?><div class="painel vazio">Não há produtos disponíveis. Cadastre produtos na área administrativa.</div><?php endif; ?>
<?php rodape(); ?>
