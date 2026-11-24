CREATE DATABASE IF NOT EXISTS supermercado
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE supermercado;

CREATE TABLE IF NOT EXISTS produtos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    descricao VARCHAR(255) NULL,
    preco DECIMAL(10,2) NOT NULL,
    quantidade INT UNSIGNED NOT NULL DEFAULT 0,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS vendas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subtotal DECIMAL(10,2) NOT NULL,
    desconto DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,
    data_venda TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS venda_itens (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    venda_id INT UNSIGNED NOT NULL,
    produto_id INT UNSIGNED NULL,
    nome_produto VARCHAR(120) NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    quantidade INT UNSIGNED NOT NULL,
    total_item DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_item_venda FOREIGN KEY (venda_id) REFERENCES vendas(id) ON DELETE CASCADE,
    CONSTRAINT fk_item_produto FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Produtos de exemplo para testar. Pode apagar se preferir.
INSERT INTO produtos (nome, descricao, preco, quantidade) VALUES
('Arroz tipo 1', 'Pacote de 5 kg', 24.90, 30),
('Feijão carioca', 'Pacote de 1 kg', 8.50, 40),
('Açúcar refinado', 'Pacote de 1 kg', 4.99, 25),
('Óleo de soja', 'Garrafa de 900 ml', 6.79, 35),
('Café tradicional', 'Pacote de 500 g', 18.90, 20);
