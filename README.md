# **Mini Locação Syspan (PHP + MySQL)**

O **Mini Sistema de Locação Syspan** é um projeto desenvolvido para prática de estudos em PHP, MySQL, HTML, CSS e JavaScript, inspirado nos sistemas da Syspan. O sistema permite o gerenciamento de clientes, equipamentos, tabela de preços, contratos e itens de contratos de locação.

## **Funcionalidades**

### Módulo de Clientes
- CRUD Completo de Clientes.
- Validação nativa de CPF e CNPJ.
- Busca automática de endereço por CEP integrando a API externa do ViaCEP.
- Máscaras de entrada para CPF/CNPJ, telefone e CEP.

### Módulo de Equipamentos
- CRUD Completo de Equipamentos.

### Módulo de Preços
- CRUD Completo de Tabela de Preços.
    - Suporte a vários preços para o mesmo equipamento.
    - Controle flexível de preços por diária.
- CRUD Completo de Itens da Tabela de Preço.

### Módulo de Contratos
- CRUD Completo de Contratos.
    - Associação direta entre cliente e tabela de preços.
- CRUD Completo de Itens de Contrato.
    - Cálculo de subtotal e valor total do contrato.

### Segurança e Arquitetura
- Helpers reutilizáveis para sanitização de dados.
- Proteção nas saídas HTML via função helper `e()` usando `htmlspecialchars`.
- Uso de Prepared Statements (PDO).
- Validações robustas em PHP.
- Sistema de registro de logs.

## **Tecnologias utilizadas**
- PHP (PDO)
- MySQL
- HTML + CSS
- JavaScript
- ViaCEP API
- XAMPP (ou similar)

## **Estrutura do Projeto**
```text
syspan-estagio-locacao/
├── projeto-locacao/
│    ├── assets/
│    ├── clientes/
│    ├── contratos/
│    ├── equipamentos/
│    ├── layout/
│    ├── precos/
│    ├── .gitignore
│    ├── conexao.php
│    ├── helpers.php
│    ├── index.php
│    └── logger.php
├── sql/
│    └── locacao.sql
└── README.md
```

## **Como executar o projeto**

### **1. Clone o repositório**
```bash
git clone https://github.com/thamiresvianna/syspan-estagio-locacao.git
```

### **2. Configure o ambiente**
- Instale o XAMPP (ou outro servidor com PHP + MySQL).
- Copie o projeto para a pasta `htdocs`.

### **3. Crie o banco de dados**
Execute o script SQL:
```bash
sql/locacao.sql
```

### **4. Configure a conexão**
Abra o arquivo:
```bash
projeto-locacao/conexao.php
```

Ajuste usuário e senha do MySQL, se necessário:
```php
$host = '127.0.0.1';
$porta = '3306';
$banco = 'syspan_locacao_estagio'; 
$usuario = 'root'; 
$senha = '';
```

### **5. Execute no navegador**
```bash
http://localhost/syspan-estagio-locacao/projeto-locacao/index.php
```

## Autora

**Thamires Vianna**

GitHub: https://github.com/thamiresvianna