SISTEMA DE SUPERMERCADO — PROJETO ACADÊMICO
Disciplina: Programação Back-End
Professor: Erik do Carmo Marques

REQUISITOS
- PHP 8 ou superior
- MySQL/MariaDB
- XAMPP (Apache e MySQL)
- Navegador e editor de código

COMO INSTALAR NO WINDOWS COM XAMPP
1. Extraia a pasta "supermercado_tde" para C:\xampp\htdocs\
2. Abra o XAMPP Control Panel.
3. Clique em Start no Apache e no MySQL.
4. Abra http://localhost/phpmyadmin
5. Clique em Importar, selecione o arquivo database.sql desta pasta e execute.
   O SQL cria o banco "supermercado", as tabelas e alguns produtos de exemplo.
6. Confira os dados de conexão em conexao.php:
   host: localhost | banco: supermercado | usuário: root | senha: vazia (padrão do XAMPP).
   Se seu MySQL tiver outra senha, altere a variável $senha.
7. Acesse http://localhost/supermercado_tde/

COMO TESTAR
1. Abra "Produtos" e cadastre um produto.
2. Edite o produto e altere a quantidade.
3. Abra "Loja do cliente", adicione produtos ao carrinho.
4. Faça uma compra de subtotal acima de R$ 200,00 para verificar os 5% de desconto.
5. Finalize e confira a baixa do estoque em "Produtos".
6. Abra "Vendas" para consultar o histórico e os detalhes.

REGRA DO DESCONTO
O sistema concede 5% quando o subtotal é ESTRITAMENTE MAIOR que R$ 200,00.
Exatamente R$ 200,00 não recebe desconto.

OBSERVAÇÕES
- O carrinho é mantido na sessão do navegador.
- A finalização usa transação no banco e bloqueia os produtos durante a conferência de estoque.
- O projeto é uma base acadêmica local. Antes de publicar na internet, é necessário adicionar autenticação administrativa, HTTPS, configuração segura e revisão de segurança.
- Não há pagamento real: a compra é uma simulação, conforme o trabalho.
- Não existe cadastro de cliente, pois não é exigido no enunciado.

ESTRUTURA
conexao.php       conexão PDO com MySQL
funcoes.php       funções comuns, sessão e proteção CSRF
index.php         painel administrativo
produtos.php      CRUD de produtos
cliente.php       catálogo da loja
carrinho.php      carrinho de compras
finalizar.php     venda, desconto e baixa no estoque
vendas.php        histórico e detalhes das vendas
database.sql      criação do banco e tabelas
css/style.css     visual do sistema

IMPORTANTE PARA A APRESENTAÇÃO
Explique ao professor como o PHP recebe os dados dos formulários, como o PDO executa consultas no MySQL, como a compra calcula o desconto e como a transação registra a venda e atualiza o estoque.
