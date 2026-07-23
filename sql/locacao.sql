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

-- Preços dos Equipamentos
CREATE TABLE IF NOT EXISTS precos (
 id INT AUTO_INCREMENT PRIMARY KEY,
 nome VARCHAR(100) NOT NULL,
 descricao VARCHAR(255) NULL,
 ativo TINYINT(1) NOT NULL DEFAULT 1,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Preços dos itens dos equipamentos (muitos preços por equipamento)
CREATE TABLE IF NOT EXISTS preco_itens (
 id INT AUTO_INCREMENT PRIMARY KEY,
 id_preco INT NOT NULL,
 id_equipamento INT NOT NULL,
 valor_diaria DECIMAL(10,2) NOT NULL,
 CONSTRAINT fk_preco
 FOREIGN KEY (id_preco) REFERENCES precos(id),
 CONSTRAINT fk_preco_equipamento
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

-- Select Contratos + Clientes
SELECT contratos.id, clientes.nome as cliente, contratos.data_inicio, contratos.data_fim, contratos.status, contratos.observacao
FROM contratos INNER JOIN clientes ON contratos.id_cliente = clientes.id;

-- Select Itens + Equipamento com subtotal
SELECT contrato_itens.id_contrato, equipamentos.descricao as equipamento, contrato_itens.diaria, contrato_itens.qtd, (contrato_itens.diaria * contrato_itens.qtd) as subtotal
FROM contrato_itens INNER JOIN equipamentos ON contrato_itens.id_equipamento = equipamentos.id;

-- Adição de Novos Dados de Clientes
ALTER TABLE clientes 
ADD tipo_pessoa ENUM('F','J') NOT NULL DEFAULT 'F' AFTER id,
ADD cpf_cnpj VARCHAR(18) NOT NULL AFTER nome,
ADD cep VARCHAR(9) NULL AFTER telefone,
ADD endereco VARCHAR(150) NULL AFTER cep,
ADD numero VARCHAR(10) NULL AFTER endereco,
ADD complemento VARCHAR(100) NULL AFTER numero,
ADD bairro VARCHAR(80) NULL AFTER complemento,
ADD cidade VARCHAR(80) NULL AFTER bairro,
ADD estado CHAR(2) NULL AFTER cidade,
ADD observacao VARCHAR(255) NULL AFTER estado,
ADD updated_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP
ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE clientes ADD UNIQUE (cpf_cnpj);

INSERT INTO clientes (tipo_pessoa, nome, cpf_cnpj, email, telefone, cep, endereco, numero, complemento, bairro, cidade, estado, observacao) 
VALUES ('J', 'Rent Obras', '63.784.367/0001-63', 'rentobras@gmail.com', '1499153-2789', '17601-290', 'Rua Cezário Nogueira Cabral', '412', 'Empresa', 'Vila Abarca', 'Tupã', 'SP', 'Comunicação somente até 15 horas.');

-- Migrar valores dos preços dos equipamentos
INSERT INTO precos (nome, descricao) VALUES ('Tabela Padrão', 'Tabela de preços inicial');

SET @id_tabela = LAST_INSERT_ID();
INSERT INTO preco_itens (id_preco, id_equipamento, valor_diaria) SELECT @id_tabela, id, diaria FROM equipamentos;
ALTER TABLE preco_itens ADD UNIQUE uk_preco_equipamento(id_preco, id_equipamento);
ALTER TABLE contratos ADD id_preco INT NULL AFTER id_cliente;
ALTER TABLE contratos ADD CONSTRAINT fk_contrato_preco FOREIGN KEY (id_preco) REFERENCES precos(id);
ALTER TABLE equipamentos DROP COLUMN diaria;