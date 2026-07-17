<?php
    require_once '../conexao.php';
    require_once '../logger.php';

    $erros = [];
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

    $descricao = $equipamento['descricao'];
    $diaria = $equipamento['diaria'];
    $ativo = $equipamento['ativo'];

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $descricao = trim($_POST["descricao"] ?? '');
        $diaria = (float) (trim($_POST["diaria"] ?? ''));
        $ativo = isset($_POST["ativo"]) ? 1 : 0;

        if(strlen($descricao) < 3 || strlen($descricao) > 120){
            $erros[] = "Descrição deve conter entre 3 e 120 caracteres.";
        }
        if($diaria <= 0){
            $erros[] = "Valor da diária inválido. O valor deve ser maior do que 0.";
        }
        if(empty($erros)){
            $sql = 'UPDATE equipamentos SET descricao = :descricao, diaria = :diaria, ativo = :ativo WHERE id = :id';
            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ":descricao" => $descricao,
                ":diaria" => $diaria,
                ":ativo" => $ativo,
                ":id" => $id
            ]);

            registrarLog("Equipamento editado: ID $id");

            header("Location: listar.php");
            exit;
        }
    }

    require_once '../layout/header.php';
?>

<h2>Editar Equipamento</h2>

<form method="POST">
    <label>Descrição:</label><br>
    <input type="text" name="descricao" value="<?= htmlspecialchars($descricao ?? '') ?>" required><br>

    <label>Diária (R$):</label><br>
    <input type="number" step="0.01" name="diaria" value="<?= htmlspecialchars($diaria ?? '') ?>" required><br>

    <label>Ativo:</label>
    <input type="checkbox" name="ativo" value="1" <?= $ativo ? 'checked' : '' ?>><br><br>

    <button type="submit">Salvar</button>
    <a class="botao-cancelar" href="listar.php">Cancelar</a>
</form>

<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($erros)) {
        foreach ($erros as $erro){
            echo "<p class='erro'>$erro</p>";
        }
    }
?>

<?php require_once '../layout/footer.php'; ?>