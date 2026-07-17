<?php
    require_once '../conexao.php';

    $pagina = max(1, (int)($_GET['pagina'] ?? 1));
    $registros_pagina  = 5;
    $offset = ($pagina - 1) * $registros_pagina;

    $sql = 'SELECT id, descricao, diaria, ativo, created_at FROM equipamentos LIMIT :registros_pagina OFFSET :offset';
    $consulta = $pdo->prepare($sql);

    $consulta->bindValue(':registros_pagina', $registros_pagina, PDO::PARAM_INT);
    $consulta->bindValue(':offset', $offset, PDO::PARAM_INT);

    $consulta->execute();
    $equipamentos = $consulta->fetchAll();

    $total_equipamentos = $pdo->query('SELECT COUNT(*) FROM equipamentos')->fetchColumn();
    $total_paginas = max(1, ceil($total_equipamentos / $registros_pagina));

    require_once '../layout/header.php';
?>

<h2>Lista de Equipamentos</h2>

<a class="links" href="novo.php">Novo Equipamento</a>

<?php if(!empty($equipamentos)): ?>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Id</th>
            <th>Descrição</th>
            <th>Diária</th>
            <th>Ativo</th>
            <th>Data de Cadastro</th>
            <th>Ações</th>
        </tr>

        <?php foreach($equipamentos as $row): ?>
            <tr>
                <td><?= (int)$row["id"] ?></td>
                <td><?= htmlspecialchars($row["descricao"]) ?></td>
                <td>R$ <?= number_format($row["diaria"], 2, ',', '.') ?></td>
                <td><?= $row["ativo"] ? 'Sim' : 'Não' ?></td>
                <td><?= date('d/m/Y H:i', strtotime($row["created_at"])) ?></td>
                <td>
                    <a class="botao-editar" href="editar.php?id=<?= (int)$row["id"] ?>">Editar</a>
                    <a class="botao-excluir" href="excluir.php?id=<?= (int)$row["id"] ?>">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <div class="paginacao">
        <?php for($i=1; $i <= $total_paginas; $i++): ?>
            <a href="?pagina=<?= $i ?>" class="<?= $i == $pagina ? 'ativa' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>

<?php else: ?>
    <p>Nenhum equipamento registrado.</p>
<?php endif; ?>

<?php require_once '../layout/footer.php'; ?>