<?php
require_once 'conexao.php';
require_once 'funcoes.php';

if (!isset($_SESSION['carrinho']) || !is_array($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();
    $acao = $_POST['acao'] ?? '';
    $id = filter_var($_POST['produto_id'] ?? null, FILTER_VALIDATE_INT);

    if ($acao === 'adicionar' && $id) {
        $quantidade = filter_var($_POST['quantidade'] ?? 1, FILTER_VALIDATE_INT);
        $stmt = $pdo->prepare("SELECT quantidade FROM produtos WHERE id=?");
        $stmt->execute([$id]);
        $produto = $stmt->fetch();
        if ($produto && $quantidade && $quantidade > 0) {
            $atual = (int)($_SESSION['carrinho'][$id] ?? 0);
            $_SESSION['carrinho'][$id] = min($atual + $quantidade, (int)$produto['quantidade']);
        }
        redirecionar('cliente.php?adicionado=1');
    }

    if ($acao === 'atualizar' && $id) {
        $quantidade = filter_var($_POST['quantidade'] ?? 0, FILTER_VALIDATE_INT);
        if (!$quantidade || $quantidade < 1) {
            unset($_SESSION['carrinho'][$id]);
        } else {
            $stmt = $pdo->prepare("SELECT quantidade FROM produtos WHERE id=?");
            $stmt->execute([$id]);
            $produto = $stmt->fetch();
            if ($produto) {
                $_SESSION['carrinho'][$id] = min($quantidade, (int)$produto['quantidade']);
            } else {
                unset($_SESSION['carrinho'][$id]);
            }
        }
        redirecionar('carrinho.php');
    }

    if ($acao === 'remover' && $id) {
        unset($_SESSION['carrinho'][$id]);
        redirecionar('carrinho.php');
    }

    if ($acao === 'limpar') {
        $_SESSION['carrinho'] = [];
        redirecionar('carrinho.php');
    }
}

$itens = [];
$subtotal = 0.0;
if ($_SESSION['carrinho']) {
    $ids = array_map('intval', array_keys($_SESSION['carrinho']));
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id IN ($placeholders) ORDER BY nome");
    $stmt->execute($ids);
    foreach ($stmt->fetchAll() as $p) {
        $qtd = (int)$_SESSION['carrinho'][$p['id']];
        $linha = (float)$p['preco'] * $qtd;
        $subtotal += $linha;
        $itens[] = ['produto' => $p, 'quantidade' => $qtd, 'total' => $linha];
    }
}
$desconto = $subtotal > 200 ? $subtotal * 0.05 : 0;
$total = $subtotal - $desconto;

cabecalho('Carrinho');
?>
<div class="titulo-pagina"><div><p class="sobretitulo">SUA COMPRA</p><h1>Carrinho de compras</h1><p>Confira os produtos antes de finalizar.</p></div></div>
<section class="painel">
<?php if (!$itens): ?>
    <div class="vazio grande"><span>🛒</span><h2>Seu carrinho está vazio</h2><p>Visite a loja e adicione produtos à sua compra.</p><a class="botao" href="cliente.php">Ir para a loja</a></div>
<?php else: ?>
    <div class="tabela-responsiva">
    <table>
        <thead><tr><th>Produto</th><th>Preço unitário</th><th>Quantidade</th><th>Total</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($itens as $item): $p = $item['produto']; ?>
            <tr>
                <td><b><?= e($p['nome']) ?></b></td>
                <td><?= dinheiro($p['preco']) ?></td>
                <td>
                    <form method="post" class="form-qtd">
                        <input type="hidden" name="csrf" value="<?= e(token_csrf()) ?>">
                        <input type="hidden" name="acao" value="atualizar">
                        <input type="hidden" name="produto_id" value="<?= (int)$p['id'] ?>">
                        <input type="number" name="quantidade" min="1" max="<?= (int)$p['quantidade'] ?>" value="<?= $item['quantidade'] ?>">
                        <button class="botao-pequeno" type="submit">Atualizar</button>
                    </form>
                </td>
                <td><?= dinheiro($item['total']) ?></td>
                <td><form method="post"><input type="hidden" name="csrf" value="<?= e(token_csrf()) ?>"><input type="hidden" name="acao" value="remover"><input type="hidden" name="produto_id" value="<?= (int)$p['id'] ?>"><button class="link-perigo" type="submit">Remover</button></form></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <div class="resumo-compra">
        <p><span>Subtotal</span><b><?= dinheiro($subtotal) ?></b></p>
        <p><span>Desconto <?= $subtotal > 200 ? '(5%)' : '' ?></span><b class="desconto">- <?= dinheiro($desconto) ?></b></p>
        <?php if ($subtotal <= 200): ?><small>O desconto de 5% é aplicado quando o subtotal ultrapassa R$ 200,00.</small><?php endif; ?>
        <p class="total"><span>Total</span><strong><?= dinheiro($total) ?></strong></p>
        <form method="post" action="finalizar.php">
            <input type="hidden" name="csrf" value="<?= e(token_csrf()) ?>">
            <button class="botao botao-largo" type="submit">Finalizar compra</button>
        </form>
        <a class="link-centro" href="cliente.php">Continuar comprando</a>
    </div>
<?php endif; ?>
</section>
<?php rodape(); ?>
