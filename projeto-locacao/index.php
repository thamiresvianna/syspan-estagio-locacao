<?php
    require_once 'layout/header.php';
?>

<h2>Bem-vindo ao Mini Sistema de Locação Syspan!</h2>

<p>Este sistema permite gerenciar clientes, equipamentos e contratos de locação de forma simples e organizada.</p>

<div class="caixa-index">
    <a class="card-index" href="clientes/listar.php">
        <h4>Clientes</h4>
        <p>Cadastre, consulte, edite e exclua os clientes.</p>
    </a>

    <a class="card-index" href="equipamentos/listar.php">
        <h4>Equipamentos</h4>
        <p>Cadastre, consulte, edite e exclua os equipamentos.</p>
    </a>

    <a class="card-index" href="precos/listar.php">
        <h4>Tabela de Preços</h4>
        <p>Cadastre e gerencie as tabelas de preços dos equipamentos.</p>
    </a>

    <a class="card-index" href="contratos/listar.php">
        <h4>Contratos</h4>
        <p>Cadastre contratos, adicione equipamentos e acompanhe as locações.</p>
    </a>
</div>

<?php require_once 'layout/footer.php'; ?>