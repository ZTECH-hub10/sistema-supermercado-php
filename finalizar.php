<?php
require_once 'conexao.php';
require_once 'funcoes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar('carrinho.php');
}
validar_csrf();

$carrinho = $_SESSION['carrinho'] ?? [];
if (!$carrinho) {
    redirecionar('carrinho.php');
}

try {
    $pdo->beginTransaction();

    $subtotal = 0.0;
    $itensVenda = [];

    foreach ($carrinho as $id => $quantidade) {
        $stmt = $pdo->prepare("SELECT id, nome, preco, quantidade FROM produtos WHERE id = ? FOR UPDATE");
        $stmt->execute([(int)$id]);
        $produto = $stmt->fetch();

        if (!$produto) {
            throw new RuntimeException('Um produto do carrinho não está mais disponível.');
        }
        $qtd = (int)$quantidade;
        if ($qtd < 1 || $qtd > (int)$produto['quantidade']) {
            throw new RuntimeException('Estoque insuficiente para: ' . $produto['nome']);
        }

        $preco = (float)$produto['preco'];
        $linha = $preco * $qtd;
        $subtotal += $linha;
        $itensVenda[] = ['id' => (int)$produto['id'], 'nome' => $produto['nome'], 'preco' => $preco, 'quantidade' => $qtd, 'total' => $linha];
    }

    $desconto = $subtotal > 200 ? round($subtotal * 0.05, 2) : 0.00;
    $total = round($subtotal - $desconto, 2);

    $stmt = $pdo->prepare("INSERT INTO vendas (subtotal, desconto, total) VALUES (?, ?, ?)");
    $stmt->execute([$subtotal, $desconto, $total]);
    $vendaId = (int)$pdo->lastInsertId();

    $stmtItem = $pdo->prepare("INSERT INTO venda_itens (venda_id, produto_id, nome_produto, preco_unitario, quantidade, total_item) VALUES (?, ?, ?, ?, ?, ?)");
    $stmtEstoque = $pdo->prepare("UPDATE produtos SET quantidade = quantidade - ? WHERE id = ?");

    foreach ($itensVenda as $item) {
        $stmtItem->execute([$vendaId, $item['id'], $item['nome'], $item['preco'], $item['quantidade'], $item['total']]);
        $stmtEstoque->execute([$item['quantidade'], $item['id']]);
    }

    $pdo->commit();
    $_SESSION['carrinho'] = [];
    $_SESSION['ultima_venda'] = $vendaId;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    cabecalho('Não foi possível finalizar');
    echo '<div class="alerta erro">' . e($e->getMessage()) . '</div><a class="botao" href="carrinho.php">Voltar ao carrinho</a>';
    rodape();
    exit;
}

cabecalho('Compra concluída');
?>
<section class="painel sucesso-compra">
    <div class="icone-sucesso">✓</div>
    <p class="sobretitulo">COMPRA CONCLUÍDA</p>
    <h1>Obrigado pela compra!</h1>
    <p>Seu pedido foi registrado e o estoque foi atualizado automaticamente.</p>
    <div class="resumo-compra resumo-final">
        <p><span>Número da venda</span><b>#<?= $vendaId ?></b></p>
        <p><span>Subtotal</span><b><?= dinheiro($subtotal) ?></b></p>
        <p><span>Desconto</span><b class="desconto">- <?= dinheiro($desconto) ?></b></p>
        <p class="total"><span>Total pago</span><strong><?= dinheiro($total) ?></strong></p>
    </div>
    <a class="botao" href="cliente.php">Voltar à loja</a>
    <a class="link-centro" href="vendas.php">Ver histórico de vendas</a>
</section>
<?php rodape(); ?>
