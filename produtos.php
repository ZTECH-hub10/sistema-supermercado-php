<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$erro = '';
$produtoEdicao = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'salvar') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
        $nome = trim((string)($_POST['nome'] ?? ''));
        $descricao = trim((string)($_POST['descricao'] ?? ''));
        $preco = filter_var($_POST['preco'] ?? null, FILTER_VALIDATE_FLOAT);
        $quantidade = filter_var($_POST['quantidade'] ?? null, FILTER_VALIDATE_INT);

        if ($nome === '' || mb_strlen($nome) > 120 || $preco === false || $preco <= 0 || $quantidade === false || $quantidade < 0) {
            $erro = 'Preencha nome, preço maior que zero e quantidade igual ou maior que zero.';
        } else {
            if ($id > 0) {
                $stmt = $pdo->prepare("UPDATE produtos SET nome=?, descricao=?, preco=?, quantidade=? WHERE id=?");
                $stmt->execute([$nome, $descricao, $preco, $quantidade, $id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO produtos (nome, descricao, preco, quantidade) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nome, $descricao, $preco, $quantidade]);
            }
            redirecionar('produtos.php?ok=1');
        }
    }

    if ($acao === 'excluir') {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        if ($id) {
            try {
                $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
                $stmt->execute([$id]);
                redirecionar('produtos.php?excluido=1');
            } catch (PDOException $e) {
                $erro = 'Este produto já aparece em uma venda e não pode ser excluído. Você pode editar a quantidade para zero.';
            }
        }
    }
}

if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->execute([(int)$_GET['editar']]);
    $produtoEdicao = $stmt->fetch() ?: null;
}

$produtos = $pdo->query("SELECT * FROM produtos ORDER BY nome")->fetchAll();
cabecalho('Produtos');
?>
<div class="titulo-pagina">
    <div><p class="sobretitulo">ADMINISTRAÇÃO</p><h1>Produtos</h1><p>Cadastre e mantenha os produtos do supermercado.</p></div>
</div>

<?php if ($erro): ?><div class="alerta erro"><?= e($erro) ?></div><?php endif; ?>
<?php if (isset($_GET['ok'])): ?><div class="alerta sucesso">Produto salvo com sucesso!</div><?php endif; ?>
<?php if (isset($_GET['excluido'])): ?><div class="alerta sucesso">Produto excluído com sucesso!</div><?php endif; ?>

<section class="painel">
    <h2><?= $produtoEdicao ? 'Editar produto' : 'Cadastrar novo produto' ?></h2>
    <form method="post" class="formulario">
        <input type="hidden" name="csrf" value="<?= e(token_csrf()) ?>">
        <input type="hidden" name="acao" value="salvar">
        <input type="hidden" name="id" value="<?= e($produtoEdicao['id'] ?? '') ?>">
        <label>Nome do produto
            <input type="text" name="nome" maxlength="120" required value="<?= e($produtoEdicao['nome'] ?? '') ?>" placeholder="Ex.: Arroz tipo 1">
        </label>
        <label>Descrição
            <input type="text" name="descricao" value="<?= e($produtoEdicao['descricao'] ?? '') ?>" placeholder="Ex.: Pacote de 5 kg">
        </label>
        <div class="duas-colunas">
            <label>Preço (R$)
                <input type="number" name="preco" min="0.01" step="0.01" required value="<?= e($produtoEdicao['preco'] ?? '') ?>" placeholder="0,00">
            </label>
            <label>Quantidade em estoque
                <input type="number" name="quantidade" min="0" step="1" required value="<?= e($produtoEdicao['quantidade'] ?? '0') ?>">
            </label>
        </div>
        <div class="acoes-form">
            <button class="botao" type="submit"><?= $produtoEdicao ? 'Atualizar produto' : 'Salvar produto' ?></button>
            <?php if ($produtoEdicao): ?><a class="botao botao-secundario" href="produtos.php">Cancelar edição</a><?php endif; ?>
        </div>
    </form>
</section>

<section class="painel">
    <div class="linha-titulo"><h2>Produtos cadastrados</h2><span class="contador"><?= count($produtos) ?> produto(s)</span></div>
    <div class="tabela-responsiva">
        <table>
            <thead><tr><th>Produto</th><th>Preço</th><th>Estoque</th><th>Situação</th><th>Ações</th></tr></thead>
            <tbody>
            <?php if (!$produtos): ?><tr><td colspan="5" class="vazio">Nenhum produto cadastrado ainda.</td></tr><?php endif; ?>
            <?php foreach ($produtos as $p): ?>
                <tr>
                    <td><b><?= e($p['nome']) ?></b><small><?= e($p['descricao']) ?></small></td>
                    <td><?= dinheiro($p['preco']) ?></td>
                    <td><?= (int)$p['quantidade'] ?></td>
                    <td><?= (int)$p['quantidade'] === 0 ? '<span class="status indisponivel">Sem estoque</span>' : ((int)$p['quantidade'] <= 5 ? '<span class="status baixo">Estoque baixo</span>' : '<span class="status disponivel">Disponível</span>') ?></td>
                    <td class="acoes">
                        <a class="botao-pequeno" href="produtos.php?editar=<?= (int)$p['id'] ?>">Editar</a>
                        <form method="post" onsubmit="return confirm('Deseja realmente excluir este produto?')">
                            <input type="hidden" name="csrf" value="<?= e(token_csrf()) ?>">
                            <input type="hidden" name="acao" value="excluir">
                            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                            <button class="botao-pequeno perigo" type="submit">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php rodape(); ?>
