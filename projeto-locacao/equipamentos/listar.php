<?php
    require_once '../conexao.php';
    require_once '../helpers.php';

    $pagina = max(1, (int)($_GET['pagina'] ?? 1));
    $registros_pagina  = 5;
    $offset = ($pagina - 1) * $registros_pagina;

    $busca  = trim($_GET['busca'] ?? '');

    $sql = 'SELECT id, codigo, descricao, categoria, marca, ativo, created_at FROM equipamentos 
            WHERE codigo LIKE :busca OR descricao LIKE :busca OR categoria LIKE :busca OR marca LIKE :busca
            ORDER BY descricao ASC LIMIT :registros_pagina OFFSET :offset';
    $consulta = $pdo->prepare($sql);

    $consulta->bindValue(':busca', "%$busca%", PDO::PARAM_STR);
    $consulta->bindValue(':registros_pagina', $registros_pagina, PDO::PARAM_INT);
    $consulta->bindValue(':offset', $offset, PDO::PARAM_INT);

    $consulta->execute();
    $equipamentos = $consulta->fetchAll();

    $sql = 'SELECT COUNT(*) FROM equipamentos WHERE codigo LIKE :busca OR descricao LIKE :busca OR categoria LIKE :busca OR marca LIKE :busca';
    $consulta = $pdo->prepare($sql);
    $consulta->execute([':busca' => "%$busca%"]);
    $total_equipamentos = $consulta->fetchColumn();
    $total_paginas = max(1, ceil($total_equipamentos / $registros_pagina));

    require_once '../layout/header.php';
?>

<h2>Lista de Equipamentos</h2>

<a class="links" href="novo.php">Novo Equipamento</a>

<form method="GET">
    <input type="text" name="busca" placeholder="Pesquisar por código, descrição, categoria ou marca..." value="<?= e($busca) ?>">

    <button type="submit">Buscar</button>
</form>

<?php if(!empty($equipamentos)): ?>
    <table>
        <tr>
            <th>Código</th>
            <th>Descrição</th>
            <th>Categoria</th>
            <th>Marca</th>
            <th>Ativo</th>
            <th>Data de Cadastro</th>
            <th>Ações</th>
        </tr>

        <?php foreach($equipamentos as $row): ?>
            <tr>
                <td><?= e($row["codigo"]) ?></td>
                <td><?= e($row["descricao"]) ?></td>
                <td><?= mostrarValor($row["categoria"]) ?></td>
                <td><?= mostrarValor($row["marca"]) ?></td>
                <td><?= $row["ativo"] ? 'Sim' : 'Não' ?></td>
                <td><?= formatarData($row["created_at"]) ?></td>
                <td>
                    <a class="botao-ver" href="ver.php?id=<?= (int)$row["id"] ?>">Visualizar</a>
                    <a class="botao-editar" href="editar.php?id=<?= (int)$row["id"] ?>">Editar</a>
                    <a class="botao-excluir" href="excluir.php?id=<?= (int)$row["id"] ?>">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <div class="paginacao">
        <?php for($i=1; $i <= $total_paginas; $i++): ?>
            <a href="?pagina=<?= $i ?>&busca=<?= e($busca) ?>" class="<?= $i == $pagina ? 'ativa' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>

<?php else: ?>
    <p>Nenhum equipamento registrado.</p>
<?php endif; ?>

<?php require_once '../layout/footer.php'; ?>