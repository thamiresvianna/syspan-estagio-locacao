<?php
    require_once '../conexao.php';
    require_once '../logger.php';
    require_once '../helpers.php';

    $erros = [];
    $id = obterId();

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

        $erros = validarEquipamento($descricao, $diaria);
        
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
    <input type="text" name="descricao" value="<?= e($descricao ?? '') ?>" required><br>

    <label>Diária (R$):</label><br>
    <input type="number" step="0.01" name="diaria" value="<?= e($diaria ?? '') ?>" required><br>

    <label>Ativo:</label>
    <input type="checkbox" name="ativo" value="1" <?= $ativo ? 'checked' : '' ?>><br><br>

    <button type="submit">Salvar</button>
    <a class="botao-cancelar" href="listar.php">Cancelar</a>
</form>

<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($erros)) {
        mostrarErros($erros);
    }
?>

<?php require_once '../layout/footer.php'; ?>