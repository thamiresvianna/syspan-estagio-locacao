<?php
    require_once '../conexao.php';
    require_once '../logger.php';
    require_once '../helpers.php';

    $erros = [];
    $id_item = obterId();

    $sql = 'SELECT preco_itens.id, preco_itens.id_preco, equipamentos.descricao, preco_itens.valor_diaria FROM preco_itens 
            INNER JOIN equipamentos ON preco_itens.id_equipamento = equipamentos.id 
            WHERE preco_itens.id = :id';
    $consulta = $pdo->prepare($sql);
    $consulta->execute([':id' => $id_item]);

    $item = $consulta->fetch();

    if(!$item){
        die("Item não encontrado.");
    }

    $valor_diaria = $item['valor_diaria'];

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $valor_diaria = (float)trim($_POST["valor_diaria"] ?? 0);

        if($valor_diaria <= 0){
            $erros[] = "Valor da diária deve ser maior que zero.";
        }

        if(empty($erros)){
            try{
                $sql = 'UPDATE preco_itens SET valor_diaria = :valor_diaria WHERE id = :id';
                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    ":valor_diaria" => $valor_diaria,
                    ":id" => $id_item
                ]);

                registrarLog("Item da tabela de preços editada: ID $id_item");

                header("Location: ver.php?id=".$item['id_preco']);
                exit;
            }
            catch(PDOException $e){
                $erro = "Erro ao editar valor da diária.";
            }
        }
    }

    require_once '../layout/header.php';
?>

<h2>Editar Valor da Diária</h2>

<form method="POST">
    <p><strong>Equipamento:</strong> <?= e($item["descricao"]) ?></p><br>

    <label>Valor da Diária:</label><br>
    <input type="number" name="valor_diaria" step="0.01" min="0.01" value="<?= e(number_format($valor_diaria, 2, '.', '')) ?>" required><br>

    <button type="submit">Salvar</button>
    <a class="botao-cancelar" href="ver.php?id=<?= (int)$item['id_preco'] ?>">Cancelar</a>
</form>

<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($erros)) {
        mostrarErros($erros);
    }
?>

<?php require_once '../layout/footer.php'; ?>