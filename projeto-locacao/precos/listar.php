<?php
    require_once '../conexao.php';
    require_once '../helpers.php';

    $pagina = max(1, (int)($_GET['pagina'] ?? 1));
    $registros_pagina  = 5;
    $offset = ($pagina - 1) * $registros_pagina;

    $busca  = trim($_GET['busca'] ?? '');

    $sql = 'SELECT id, nome, descricao, ativo, created_at FROM precos WHERE nome LIKE :busca OR descricao LIKE :busca
            ORDER BY nome ASC LIMIT :registros_pagina OFFSET :offset';
    $consulta = $pdo->prepare($sql);

    $consulta->bindValue(':busca', "%$busca%", PDO::PARAM_STR);
    $consulta->bindValue(':registros_pagina', $registros_pagina, PDO::PARAM_INT);
    $consulta->bindValue(':offset', $offset, PDO::PARAM_INT);

    $consulta->execute();
    $precos = $consulta->fetchAll();

    $sql = 'SELECT COUNT(*) FROM precos WHERE nome LIKE :busca OR descricao LIKE :busca';
    $consulta = $pdo->prepare($sql);
    $consulta->execute([':busca' => "%$busca%"]);
    $total_precos = $consulta->fetchColumn();
    $total_paginas = max(1, ceil($total_precos / $registros_pagina));

    require_once '../layout/header.php';
?>

<h2>Lista de Preços</h2>

<a class="links" href="novo.php">Novo Preço</a>

<form method="GET">
    <input type="text" name="busca" placeholder="Pesquisar por nome ou descrição..." value="<?= e($busca) ?>">

    <button type="submit">Buscar</button>
</form>

<?php if(!empty($precos)): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Ativo</th>
            <th>Data de Cadastro</th>
            <th>Ações</th>
        </tr>

        <?php foreach($precos as $row): ?>
            <tr>
                <td><?= (int)$row["id"] ?></td>
                <td><?= e($row["nome"]) ?></td>
                <td><?= e($row["descricao"]) ?></td>
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
            <a href="?pagina=<?= $i ?>&busca=<?= e($busca) ?>" class="<?= $i == $pagina ? 'ativa' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>

<?php else: ?>
    <p>Nenhum preço registrado.</p>
<?php endif; ?>

<?php require_once '../layout/footer.php'; ?>