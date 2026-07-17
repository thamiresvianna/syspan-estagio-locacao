<?php
    require_once '../conexao.php';

    $pagina = max(1, (int)($_GET['pagina'] ?? 1));
    $registros_pagina  = 5;
    $offset = ($pagina - 1) * $registros_pagina;

    $sql = 'SELECT id, nome, email, telefone, created_at FROM clientes LIMIT :registros_pagina OFFSET :offset';
    $consulta = $pdo->prepare($sql);

    $consulta->bindValue(':registros_pagina', $registros_pagina, PDO::PARAM_INT);
    $consulta->bindValue(':offset', $offset, PDO::PARAM_INT);

    $consulta->execute();
    $clientes = $consulta->fetchAll();

    $total_clientes = $pdo->query('SELECT COUNT(*) FROM clientes')->fetchColumn();
    $total_paginas = max(1, ceil($total_clientes / $registros_pagina));

    require_once '../layout/header.php';
?>

<h2>Lista de Clientes</h2>

<a class="links" href="novo.php">Novo Cliente</a>

<?php if(!empty($clientes)): ?>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Id</th>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Telefone</th>
            <th>Data de Cadastro</th>
            <th>Ações</th>
        </tr>

        <?php foreach($clientes as $row): ?>
            <tr>
                <td><?= (int)$row["id"] ?></td>
                <td><?= htmlspecialchars($row["nome"]) ?></td>
                <td><?= htmlspecialchars($row["email"]) ?></td>
                <td><?= htmlspecialchars($row["telefone"]) ?></td>
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
    <p>Nenhum cliente registrado.</p>
<?php endif; ?>

<?php require_once '../layout/footer.php'; ?>