<?php
    require_once '../conexao.php';
    require_once '../logger.php';
    require_once '../helpers.php';

    $erros = [];

    $descricao = '';
    $diaria = '';
    $ativo = 0;

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $descricao = trim($_POST["descricao"] ?? '');
        $diaria = (float) (trim($_POST["diaria"] ?? ''));
        $ativo = isset($_POST["ativo"]) ? 1 : 0;
        
        $erros = validarEquipamento($descricao, $diaria);

        if(empty($erros)){
            try{
                $sql = 'INSERT INTO equipamentos (descricao, diaria, ativo) VALUES (:descricao, :diaria, :ativo)';
                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    ":descricao" => $descricao,
                    ":diaria" => $diaria,
                    ":ativo" => $ativo
                ]);

                registrarLog("Equipamento cadastrado: $descricao");

                header("Location: listar.php");
                exit;
            }
            catch(PDOException $e){
                $erros[] = "Erro ao cadastrar equipamento.";
            }    
        }
    }

    require_once '../layout/header.php';
?>

<h2>Inserir Equipamento</h2>

<form method="POST">
    <label>Descrição:</label><br>
    <input type="text" name="descricao" value="<?= e($descricao ?? '') ?>" required><br>

    <label>Diária (R$):</label><br>
    <input type="number" step="0.01" name="diaria" value="<?= ($diaria > 0) ? e($diaria) : '' ?>" required><br>
    
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