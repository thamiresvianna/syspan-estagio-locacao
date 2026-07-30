<?php
    require_once '../conexao.php';
    require_once '../helpers.php';

    $status = trim($_GET['status'] ?? '');
    $busca  = trim($_GET['busca'] ?? '');

    $pagina = max(1, (int)($_GET['pagina'] ?? 1));
    $registros_pagina  = 5;
    $offset = ($pagina - 1) * $registros_pagina;

    $sql = 'SELECT contratos.id, clientes.nome AS cliente, clientes.cpf_cnpj, COUNT(contrato_itens.id) AS quantidade, 
            precos.nome as tabela_preco, contratos.data_inicio, contratos.data_fim, contratos.status, contratos.created_at 
            FROM contratos 
            INNER JOIN clientes ON contratos.id_cliente = clientes.id
            LEFT JOIN contrato_itens ON contratos.id = contrato_itens.id_contrato
            LEFT JOIN precos ON contratos.id_preco = precos.id';

    $where_consulta = [];

    if($status !== ''){
        $where_consulta[] = 'contratos.status = :status';
    }

    if($busca !== ''){
        $where_consulta[] = '(clientes.nome LIKE :busca OR clientes.cpf_cnpj LIKE :busca OR 
                            CAST(contratos.id AS CHAR) LIKE :busca OR precos.nome LIKE :busca)';
    }

    if(!empty($where_consulta)){
        $sql .= ' WHERE ' . implode(' AND ', $where_consulta);
    }

    $sql .= ' GROUP BY contratos.id';
    $sql .= ' ORDER BY contratos.id DESC LIMIT :registros_pagina OFFSET :offset';
    $consulta = $pdo->prepare($sql);

    if($status !== ''){
        $consulta->bindValue(':status', $status);
    }
    if($busca !== ''){
        $consulta->bindValue(':busca', "%$busca%");
    }

    $consulta->bindValue(':registros_pagina', $registros_pagina, PDO::PARAM_INT);
    $consulta->bindValue(':offset', $offset, PDO::PARAM_INT);

    $consulta->execute();
    $contratos = $consulta->fetchAll();

    $sqlCount = 'SELECT COUNT(*) FROM contratos 
                INNER JOIN clientes ON contratos.id_cliente = clientes.id 
                LEFT JOIN precos ON contratos.id_preco = precos.id';
        
    if(!empty($where_consulta)){
        $sqlCount .= ' WHERE ' . implode(' AND ', $where_consulta);
    }

    $consultaCount = $pdo->prepare($sqlCount);

    if($status !== ''){
        $consultaCount->bindValue(':status', $status);
    }
    if($busca !== ''){
        $consultaCount->bindValue(':busca', "%$busca%");
    }

    $consultaCount->execute();
    $total_contratos = $consultaCount->fetchColumn();
    
    $total_paginas = max(1, ceil($total_contratos / $registros_pagina));

    require_once '../layout/header.php';
?>

<h2>Lista de Contratos</h2>

<a class="links" href="novo.php">Novo Contrato</a><br><br>

<form method="GET" class="filtros">
    <input type="text" name="busca" placeholder="Pesquisar por cliente, CPF/CNPJ, contrato ou tabela de preços..." value="<?= e($busca) ?>">

    <select name="status">
        <option value="">Todos os status</option>
        <option value="AGENDADO" <?= $status === 'AGENDADO' ? 'selected' : '' ?>>Agendado</option>
        <option value="ATIVO" <?= $status === 'ATIVO' ? 'selected' : '' ?>>Ativo</option>
        <option value="ENCERRADO" <?= $status === 'ENCERRADO' ? 'selected' : '' ?>>Encerrado</option>
    </select>

    <button type="submit">Buscar</button>
</form>

<?php if(!empty($contratos)): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>CPF/CNPJ</th>
            <th>Equipamentos</th>
            <th>Tabela</th>
            <th>Status</th>
            <th>Data de Cadastro</th>
            <th>Ações</th>
        </tr>

        <?php foreach($contratos as $row): ?>
            <?php $status_atual = calcularStatusContrato($row['data_inicio'], $row['data_fim']); ?>
            <tr>
                <td><?= (int)$row["id"] ?></td>
                <td><?= e($row["cliente"]) ?></td>
                <td><?= e(formatarCpfCnpj($row["cpf_cnpj"])) ?></td>
                <td><?= e((int)($row["quantidade"])) ?><?= $row["quantidade"] == 1 ? ' equipamento' : ' equipamentos' ?></td>
                <td><?= mostrarValor($row["tabela_preco"]) ?></td>
                <td><span class="status <?= strtolower($status_atual) ?>"><?= e($status_atual) ?></span></td>
                <td><?= formatarData($row["created_at"]) ?></td>
                <td>
                    <a class="botao-ver" href="ver.php?id=<?= (int)$row["id"] ?>">Ver Itens</a>
                    <a class="botao-editar" href="editar.php?id=<?= (int)$row["id"] ?>">Editar</a>
                    <a class="botao-excluir" href="excluir.php?id=<?= (int)$row["id"] ?>">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <div class="paginacao">
        <?php for($i=1; $i <= $total_paginas; $i++): ?>
            <a href="?pagina=<?= $i ?>&busca=<?= urlencode($busca) ?>&status=<?= urlencode($status) ?>" class="<?= $i == $pagina ? 'ativa' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>

<?php else: ?>
    <p>Nenhum contrato registrado.</p>
<?php endif; ?>

<?php require_once '../layout/footer.php'; ?>