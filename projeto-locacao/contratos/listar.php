<?php
    require_once '../conexao.php';
    require_once '../helpers.php';

    $status = trim($_GET['status'] ?? '');

    $pagina = max(1, (int)($_GET['pagina'] ?? 1));
    $registros_pagina  = 5;
    $offset = ($pagina - 1) * $registros_pagina;

    $sql = 'SELECT contratos.id, clientes.nome AS cliente, contratos.data_inicio, contratos.data_fim, contratos.status, contratos.observacao, contratos.created_at 
            FROM contratos INNER JOIN clientes ON contratos.id_cliente = clientes.id';

    if($status !== ''){
        $sql .= ' WHERE contratos.status = :status';
    }

    $sql .= ' LIMIT :registros_pagina OFFSET :offset';
    $consulta = $pdo->prepare($sql);

    if($status !== ''){
        $consulta->bindValue(':status', $status);
    }

    $consulta->bindValue(':registros_pagina', $registros_pagina, PDO::PARAM_INT);
    $consulta->bindValue(':offset', $offset, PDO::PARAM_INT);

    $consulta->execute();
    $contratos = $consulta->fetchAll();

    if($status !== ''){
        $sqlCount = 'SELECT COUNT(*) FROM contratos WHERE status = :status';
        $consultaCount = $pdo->prepare($sqlCount);
        $consultaCount->bindValue(':status', $status);
        $consultaCount->execute();

        $total_contratos = $consultaCount->fetchColumn();
    } else {
        $total_contratos = $pdo->query('SELECT COUNT(*) FROM contratos')->fetchColumn();
    }

    $total_paginas = max(1, ceil($total_contratos / $registros_pagina));

    require_once '../layout/header.php';
?>

<h2>Lista de Contratos</h2>

<a class="links" href="novo.php">Novo Contrato</a><br><br>

<form method="GET">
    <label>Status:</label>
    <select name="status">
        <option value="">Todos</option>
        <option value="AGENDADO" <?= $status === 'AGENDADO' ? 'selected' : '' ?>>Agendado</option>
        <option value="ATIVO" <?= $status === 'ATIVO' ? 'selected' : '' ?>>Ativo</option>
        <option value="ENCERRADO" <?= $status === 'ENCERRADO' ? 'selected' : '' ?>>Encerrado</option>
    </select><br><br>

    <button type="submit">Filtrar</button>
</form>

<?php if(!empty($contratos)): ?>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Data de Início</th>
            <th>Data de Fim</th>
            <th>Status</th>
            <th>Observações</th>
            <th>Data de Cadastro</th>
            <th>Ações</th>
        </tr>

        <?php foreach($contratos as $row): ?>
            <?php $status_atual = calcularStatusContrato($row['data_inicio'], $row['data_fim']); ?>
            <tr>
                <td><?= (int)$row["id"] ?></td>
                <td><?= e($row["cliente"]) ?></td>
                <td><?= date('d/m/Y', strtotime($row["data_inicio"])) ?></td>
                <td><?= date('d/m/Y', strtotime($row["data_fim"])) ?></td>
                <td><?= e($status_atual) ?></td>
                <td><?= !empty($row["observacao"]) ? e($row["observacao"]) : '-' ?></td>
                <td><?= date('d/m/Y H:i', strtotime($row["created_at"])) ?></td>
                <td>
                    <a class="botao-ver" href="ver.php?id=<?= (int)$row["id"] ?>">Ver Itens</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <div class="paginacao">
        <?php for($i=1; $i <= $total_paginas; $i++): ?>
            <a href="?pagina=<?= $i ?>&status=<?=  urlencode($status) ?>" class="<?= $i == $pagina ? 'ativa' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>

<?php else: ?>
    <p>Nenhum contrato registrado.</p>
<?php endif; ?>

<?php require_once '../layout/footer.php'; ?>