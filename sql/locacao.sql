CREATE DATABASE IF NOT EXISTS syspan_locacao_estagio
 DEFAULT CHARACTER SET utf8mb4
 DEFAULT COLLATE utf8mb4_general_ci;
USE syspan_locacao_estagio;

-- Clientes
CREATE TABLE IF NOT EXISTS clientes (
 id INT AUTO_INCREMENT PRIMARY KEY,
 nome VARCHAR(120) NOT NULL,
 email VARCHAR(120) NOT NULL,
 telefone VARCHAR(20) NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE UNIQUE INDEX uk_clientes_email ON clientes(email);

-- Equipamentos
CREATE TABLE IF NOT EXISTS equipamentos (
 id INT AUTO_INCREMENT PRIMARY KEY,
 descricao VARCHAR(120) NOT NULL,
 diaria DECIMAL(10,2) NOT NULL,
 ativo TINYINT(1) NOT NULL DEFAULT 1,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Contratos de Locação (simplificado)
CREATE TABLE IF NOT EXISTS contratos (
 id INT AUTO_INCREMENT PRIMARY KEY,
 id_cliente INT NOT NULL,
 data_inicio DATE NOT NULL,
 data_fim DATE NOT NULL,
 status VARCHAR(15) NOT NULL DEFAULT 'AGENDADO',
 observacao VARCHAR(255) NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_contratos_cliente
 FOREIGN KEY (id_cliente) REFERENCES clientes(id)
);

-- Itens do contrato (muitos equipamentos por contrato)
CREATE TABLE IF NOT EXISTS contrato_itens (
 id INT AUTO_INCREMENT PRIMARY KEY,
 id_contrato INT NOT NULL,
 id_equipamento INT NOT NULL,
 diaria DECIMAL(10,2) NOT NULL,
 qtd INT NOT NULL DEFAULT 1,
 CONSTRAINT fk_itens_contrato
 FOREIGN KEY (id_contrato) REFERENCES contratos(id),
 CONSTRAINT fk_itens_equip
 FOREIGN KEY (id_equipamento) REFERENCES equipamentos(id)
);

-- Inserção de Clientes
INSERT INTO clientes (nome, email, telefone) VALUES ('Marcelo Dias', 'marcelodias@gmail.com', '1498610-2385');
INSERT INTO clientes (nome, email, telefone) VALUES ('Eduardo Moreira', 'eduardomoreira@gmail.com', '1498104-1361');
INSERT INTO clientes (nome, email, telefone) VALUES ('Renata Santos', 'renatasantos@gmail.com', '1498686-4038');
INSERT INTO clientes (nome, email, telefone) VALUES ('Sarah Gonçalves', 'sarahg@gmail.com', '1499619-7297');
INSERT INTO clientes (nome, email, telefone) VALUES ('Luisa Marcon', 'luisa@gmail.com', '1498574-0147');

-- Inserção de Equipamentos
INSERT INTO equipamentos (descricao, diaria) VALUES ('Gerador de Energia', 230.00);
INSERT INTO equipamentos (descricao, diaria) VALUES ('Martelo Demolidor', 35.00);
INSERT INTO equipamentos (descricao, diaria) VALUES ('Plataforma Elevatória Articulada', 550.00);
INSERT INTO equipamentos (descricao, diaria) VALUES ('Rolo Compactador', 250.00);
INSERT INTO equipamentos (descricao, diaria) VALUES ('Betoneira', 19.00);

-- Inserção de Contratos
INSERT INTO contratos (id_cliente, data_inicio, data_fim, status, observacao) VALUES (1, '2026-05-18', '2026-05-22', 'ATIVO', 'Contrato em andamento');
INSERT INTO contratos (id_cliente, data_inicio, data_fim, status, observacao) VALUES (3, '2026-06-01', '2026-06-05', 'AGENDADO', 'Contrato futuro');

-- Inserção de Itens do Contratos
INSERT INTO contrato_itens (id_contrato, id_equipamento, diaria, qtd) VALUES (1, 1, 230.00, 3);
INSERT INTO contrato_itens (id_contrato, id_equipamento, diaria, qtd) VALUES (1, 2, 35.00, 2);
INSERT INTO contrato_itens (id_contrato, id_equipamento, diaria, qtd) VALUES (2, 3, 550.00, 1);
INSERT INTO contrato_itens (id_contrato, id_equipamento, diaria, qtd) VALUES (2, 5, 19.00, 3);

select * from clientes;
select * from equipamentos;
select * from contratos;
select * from contrato_itens;

-- Select Contratos + Clientes
SELECT contratos.id, clientes.nome as cliente, contratos.data_inicio, contratos.data_fim, contratos.status, contratos.observacao
FROM contratos INNER JOIN clientes ON contratos.id_cliente = clientes.id;

-- Select Itens + Equipamento com subtotal
SELECT contrato_itens.id_contrato, equipamentos.descricao as equipamento, contrato_itens.diaria, contrato_itens.qtd, (contrato_itens.diaria * contrato_itens.qtd) as subtotal
FROM contrato_itens INNER JOIN equipamentos ON contrato_itens.id_equipamento = equipamentos.id;