<?php
    require_once '../conexao.php';
    require_once '../logger.php';

    $id = (int) ($_GET['id'] ?? 0);

    if($id <= 0){
        die("ID inválido.");
    }

    $sql = 'SELECT id, nome, email, telefone, created_at FROM clientes WHERE id = :id';
    $consulta = $pdo->prepare($sql);
    $consulta -> execute([':id' => $id]);

    $cliente = $consulta->fetch();

    if(!$cliente){
        die("Cliente não encontrado.");
    }

    $erro = '';

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $sql = 'SELECT COUNT(*) FROM contratos WHERE id_cliente = :id';
        $consulta = $pdo->prepare($sql);
        $consulta -> execute([':id' => $id]);

        $total_contratos = $consulta->fetchColumn();

        if($total_contratos > 0){
            $erro = "Não é possível excluir este cliente porque ele possui contratos vinculados.";
        } else {
            $sql = 'DELETE FROM clientes WHERE id = :id';
            $stmt = $pdo->prepare($sql);

            $stmt->execute([":id" => $id]);

            if($stmt->rowCount() === 0){
                die("Erro ao excluir cliente.");
            }

            registrarLog("Cliente excluído: ID $id");

            header("Location: listar.php");
            exit;
        }
    }

    require_once '../layout/header.php';
?>

<h2>Excluir Cliente</h2>

<p>Tem certeza que deseja excluir o cliente: <strong><?= htmlspecialchars($cliente["nome"]) ?></strong>?</p>

<form method="POST">
    <button type="submit">Excluir</button>
    <a class="botao-cancelar" href="listar.php">Cancelar</a>
</form>

<?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($erro)): ?>
    <p class="erro"><?= htmlspecialchars($erro) ?></p>
<?php endif; ?>

<?php require_once '../layout/footer.php'; ?>