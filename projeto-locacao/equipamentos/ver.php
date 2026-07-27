<?php
    require_once '../conexao.php';
    require_once '../helpers.php';

    $id = obterId();

    $sql = 'SELECT id, codigo, descricao, categoria, marca, modelo, numero_serie, ativo, observacao, created_at, updated_at 
            FROM equipamentos WHERE id = :id';
    $consulta = $pdo->prepare($sql);
    $consulta -> execute([':id' => $id]);

    $equipamento = $consulta->fetch();

    if(!$equipamento){
        die("Equipamento não encontrado.");
    }

    require_once '../layout/header.php';
?>

<h2>Detalhes do Equipamento <?= $equipamento['id'] ?></h2>

<div>
    <p><strong>Código:</strong> <?= e($equipamento["codigo"]) ?></p>
    <p><strong>Descrição:</strong> <?= e($equipamento["descricao"]) ?></p>
    <p><strong>Categoria:</strong> <?= !empty($equipamento["categoria"]) ? e($equipamento["categoria"]) : '-' ?></p>
    <p><strong>Marca:</strong> <?= !empty($equipamento["marca"]) ? e($equipamento["marca"]) : '-' ?></p>
    <p><strong>Modelo:</strong> <?= !empty($equipamento["modelo"]) ? e($equipamento["modelo"]) : '-' ?></p>
    <p><strong>Número de Série:</strong> <?= !empty($equipamento["numero_serie"]) ? e($equipamento["numero_serie"]) : '-' ?></p>
    <p><strong>Ativo:</strong> <?= e($equipamento["ativo"] ? 'Sim' : 'Não') ?></p><br>

    <h3>Informações Adicionais</h3>
    <p><strong>Observação:</strong> <?= !empty($equipamento["observacao"]) ? e($equipamento["observacao"]) : '-' ?></p>
    <p><strong>Cadastrado em:</strong> <?= date('d/m/Y - H:i', strtotime($equipamento["created_at"])) ?></p>
    <p><strong>Atualizado em:</strong> <?= !empty($equipamento["updated_at"]) ? date('d/m/Y - H:i', strtotime($equipamento["updated_at"])) : '-' ?></p>
</div>

<br><a class="links" href="listar.php">Voltar</a>

<?php require_once '../layout/footer.php'; ?>