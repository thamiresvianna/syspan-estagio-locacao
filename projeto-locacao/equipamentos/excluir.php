<?php
    require_once '../conexao.php';
    require_once '../logger.php';

    $id = (int) ($_GET['id'] ?? 0);

    if($id <= 0){
        die("ID inválido.");
    }

    $sql = 'SELECT id, descricao, diaria, ativo, created_at FROM equipamentos WHERE id = :id';
    $consulta = $pdo->prepare($sql);
    $consulta->execute([':id' => $id]);

    $equipamento = $consulta->fetch();

    if(!$equipamento){
        die("Equipamento não encontrado.");
    }

    $erro = '';

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $sql = 'SELECT COUNT(*) FROM contrato_itens WHERE id_equipamento = :id';
        $consulta = $pdo->prepare($sql);
        $consulta -> execute([':id' => $id]);

        $total_itens = $consulta->fetchColumn();

        if($total_itens > 0){
            $erro = "Não é possível excluir este equipamento porque ele possui contratos vinculados.";
        } else {
            $sql = 'DELETE FROM equipamentos WHERE id = :id';
            $stmt = $pdo->prepare($sql);

            $stmt->execute([":id" => $id]);

            if($stmt->rowCount() === 0){
                die("Erro ao excluir equipamento.");
            }

            registrarLog("Equipamento excluído: ID $id");

            header("Location: listar.php");
            exit;
        }
    }

    require_once '../layout/header.php';
?>

<h2>Excluir Equipamento</h2>

<p>Tem certeza que deseja excluir o equipamento: <strong><?= htmlspecialchars($equipamento["descricao"]) ?></strong>?</p>

<form method="POST">
    <button type="submit">Excluir</button>
    <a class="botao-cancelar" href="listar.php">Cancelar</a>
</form>

<?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($erro)): ?>
    <p class="erro"><?= htmlspecialchars($erro) ?></p>
<?php endif; ?>

<?php require_once '../layout/footer.php'; ?>