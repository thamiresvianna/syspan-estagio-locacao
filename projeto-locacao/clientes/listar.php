<?php
    require_once '../conexao.php';
    require_once '../helpers.php';

    $pagina = max(1, (int)($_GET['pagina'] ?? 1));
    $registros_pagina  = 5;
    $offset = ($pagina - 1) * $registros_pagina;

    $busca  = trim($_GET['busca'] ?? '');

    $sql = 'SELECT id, tipo_pessoa, nome, cpf_cnpj, email, telefone, cep, cidade, estado, created_at FROM clientes 
            WHERE nome LIKE :busca OR cpf_cnpj LIKE :busca OR email LIKE :busca
            ORDER BY nome ASC LIMIT :registros_pagina OFFSET :offset';
    $consulta = $pdo->prepare($sql);

    $consulta->bindValue(':busca', "%$busca%", PDO::PARAM_STR);
    $consulta->bindValue(':registros_pagina', $registros_pagina, PDO::PARAM_INT);
    $consulta->bindValue(':offset', $offset, PDO::PARAM_INT);

    $consulta->execute();
    $clientes = $consulta->fetchAll();

    $sql = 'SELECT COUNT(*) FROM clientes WHERE nome LIKE :busca OR cpf_cnpj LIKE :busca OR email LIKE :busca';
    $consulta = $pdo->prepare($sql);
    $consulta->execute([':busca' => "%$busca%"]);
    $total_clientes = $consulta->fetchColumn();
    $total_paginas = max(1, ceil($total_clientes / $registros_pagina));

    require_once '../layout/header.php';
?>

<h2>Lista de Clientes</h2>

<a class="links" href="novo.php">Novo Cliente</a>

<form method="GET">
    <input type="text" name="busca" placeholder="Pesquisar por nome, CPF/CNPJ, ou email..." value="<?= e($busca) ?>">

    <button type="submit">Buscar</button>
</form>

<?php if(!empty($clientes)): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Tipo de Pessoa</th>
            <th>Nome</th>
            <th>CPF/CNPJ</th>
            <th>E-mail</th>
            <th>Telefone</th>
            <th>CEP</th>
            <th>Cidade</th>
            <th>Estado</th>
            <th>Data de Cadastro</th>
            <th>Ações</th>
        </tr>

        <?php foreach($clientes as $row): ?>
            <tr>
                <td><?= (int)$row["id"] ?></td>
                <td><?= $row["tipo_pessoa"] == 'F' ? 'Pessoa Física' : 'Pessoa Jurídica' ?></td>
                <td><?= e($row["nome"]) ?></td>
                <td><?= e(formatarCpfCnpj($row["cpf_cnpj"])) ?></td>
                <td><?= e($row["email"]) ?></td>
                <td><?= e(formatarTelefone($row["telefone"])) ?></td>
                <td><?= !empty($row["cep"]) ? e($row["cep"]) : '-' ?></td>
                <td><?= !empty($row["cidade"]) ? e($row["cidade"]) : '-' ?></td>
                <td><?= !empty($row["estado"]) ? e($row["estado"]) : '-' ?></td>
                <td><?= date('d/m/Y H:i', strtotime($row["created_at"])) ?></td>
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
    <p>Nenhum cliente registrado.</p>
<?php endif; ?>

<?php require_once '../layout/footer.php'; ?>