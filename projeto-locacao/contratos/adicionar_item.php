<?php
    require_once '../conexao.php';
    require_once '../logger.php';
    require_once '../helpers.php';

    $id_contrato = obterId();

    $sql = 'SELECT id FROM contratos WHERE id = :id';
    $consulta = $pdo->prepare($sql);
    $consulta->execute([':id' => $id_contrato]);
    $contrato = $consulta->fetch();

    if(!$contrato){
        die("Contrato não encontrado.");
    }

    $sql = 'SELECT id, descricao, diaria FROM equipamentos WHERE ativo = 1';
    $consulta = $pdo->prepare($sql);
    $consulta->execute();
    $equipamentos = $consulta->fetchAll();

    $erros = [];

    $id_equipamento = '';
    $qtd = 1;

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $id_equipamento = (int) trim($_POST["id_equipamento"] ?? '');
        $qtd = (int) trim($_POST["qtd"] ?? '');

        if($id_equipamento <= 0){
            $erros[] = "Equipamento inválido.";
        }
        if($qtd <= 0){
            $erros[] = "Quantidade deve ser maior que zero.";
        }

        if(empty($erros)){
            $sql = 'SELECT diaria FROM equipamentos WHERE id = :id AND ativo = 1';
            $consulta = $pdo->prepare($sql);
            $consulta->execute([":id" => $id_equipamento]);
            $equipamento = $consulta->fetch();

            if(!$equipamento){
                die("Equipamento não encontrado.");
            } else {
                $diaria = $equipamento['diaria'];

                $sql = 'INSERT INTO contrato_itens (id_contrato, id_equipamento, diaria, qtd) 
                        VALUES (:id_contrato, :id_equipamento, :diaria, :qtd)';
                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    ":id_contrato" => $id_contrato,
                    ":id_equipamento" => $id_equipamento,
                    ":diaria" => $diaria,
                    ":qtd" => $qtd
                ]);

                registrarLog("Item $id_equipamento adicionado ao contrato: $id_contrato");

                header("Location: ver.php?id=$id_contrato");
                exit;
            }
        }
    }

    require_once '../layout/header.php';
?>

<h2>Adicionar Item ao Contrato <?= $id_contrato ?></h2>

<form method="POST">
    <label>Equipamento:</label><br>
    <select name="id_equipamento" required>
        <option value="" disabled <?= empty($id_equipamento) ? 'selected' : '' ?>>- Selecione um equipamento -</option>
        <?php foreach ($equipamentos as $equipamento): ?>
            <option value="<?= e($equipamento['id']) ?>" <?= $id_equipamento == $equipamento['id'] ? 'selected' : '' ?>>
                <?= e($equipamento['descricao']) ?> (R$ <?= number_format($equipamento["diaria"], 2, ',', '.') ?>)
            </option>
        <?php endforeach; ?>
    </select><br>

    <label>Quantidade:</label><br>
    <input type="number" name="qtd" min="1" value="<?= e($qtd ?? '') ?>" required><br>

    <button type="submit">Salvar</button>
    <a class="botao-cancelar" href="ver.php?id=<?= $id_contrato ?>">Cancelar</a>
</form>

<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($erros)) {
        mostrarErros($erros);
    }
?>

<?php require_once '../layout/footer.php'; ?>