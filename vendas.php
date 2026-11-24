<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$vendas = $pdo->query("SELECT * FROM vendas ORDER BY data_venda DESC")->fetchAll();
$detalhe = null;
$itens = [];
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM vendas WHERE id=?");
    $stmt->execute([(int)$_GET['id']]);
    $detalhe = $stmt->fetch();
    if ($detalhe) {
        $stmt = $pdo->prepare("SELECT * FROM venda_itens WHERE venda_id=? ORDER BY id");
        $stmt->execute([(int)$detalhe['id']]);
        $itens = $stmt->fetchAll();
    }
}
cabecalho('Histórico de vendas');
?>
<div class="titulo-pagina"><div><p class="sobretitulo">ADMINISTRAÇÃO</p><h1>Histórico de vendas</h1><p>Consulte as compras registradas no sistema.</p></div></div>
<?php if ($detalhe): ?>
<section class="painel">
    <div class="linha-titulo"><h2>Venda #<?= (int)$detalhe['id'] ?></h2><a href="vendas.php">← Voltar ao histórico</a></div>
    <p class="texto-secundario">Data: <?= date('d/m/Y H:i', strtotime($detalhe['data_venda'])) ?></p>
    <div class="tabela-responsiva"><table><thead><tr><th>Produto</th><th>Preço unitário</th><th>Quantidade</th><th>Total</th></tr></thead><tbody>
    <?php foreach ($itens as $i): ?><tr><td><?= e($i['nome_produto']) ?></td><td><?= dinheiro($i['preco_unitario']) ?></td><td><?= (int)$i['quantidade'] ?></td><td><?= dinheiro($i['total_item']) ?></td></tr><?php endforeach; ?>
    </tbody></table></div>
    <div class="resumo-compra"><p><span>Subtotal</span><b><?= dinheiro($detalhe['subtotal']) ?></b></p><p><span>Desconto</span><b class="desconto">- <?= dinheiro($detalhe['desconto']) ?></b></p><p class="total"><span>Total</span><strong><?= dinheiro($detalhe['total']) ?></strong></p></div>
</section>
<?php else: ?>
<section class="painel">
    <div class="tabela-responsiva"><table><thead><tr><th>Venda</th><th>Data</th><th>Subtotal</th><th>Desconto</th><th>Total</th><th></th></tr></thead><tbody>
    <?php if (!$vendas): ?><tr><td colspan="6" class="vazio">Nenhuma venda realizada ainda.</td></tr><?php endif; ?>
    <?php foreach ($vendas as $v): ?><tr><td><b>#<?= (int)$v['id'] ?></b></td><td><?= date('d/m/Y H:i', strtotime($v['data_venda'])) ?></td><td><?= dinheiro($v['subtotal']) ?></td><td><?= dinheiro($v['desconto']) ?></td><td><b><?= dinheiro($v['total']) ?></b></td><td><a class="botao-pequeno" href="vendas.php?id=<?= (int)$v['id'] ?>">Detalhes</a></td></tr><?php endforeach; ?>
    </tbody></table></div>
</section>
<?php endif; ?>
<?php rodape(); ?>
