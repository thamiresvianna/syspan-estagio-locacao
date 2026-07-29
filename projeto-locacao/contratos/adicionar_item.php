<?php
    require_once '../conexao.php';
    require_once '../logger.php';
    require_once '../helpers.php';

    $id_contrato = obterId();

    $sql = 'SELECT id, id_preco FROM contratos WHERE id = :id';
    $consulta = $pdo->prepare($sql);
    $consulta->execute([':id' => $id_contrato]);
    $contrato = $consulta->fetch();

    if(!$contrato){
        die("Contrato não encontrado.");
    }

    if(empty($contrato['id_preco'])){
        die("Contrato não possui tabela de preços vinculada.");
    }

    $sql = 'SELECT equipamentos.id, equipamentos.descricao, preco_itens.valor_diaria 
            FROM equipamentos INNER JOIN preco_itens ON equipamentos.id = preco_itens.id_equipamento
            WHERE equipamentos.ativo = 1 AND preco_itens.id_preco = :id_preco';
    $consulta = $pdo->prepare($sql);
    $consulta->execute([':id_preco' => $contrato['id_preco']]);
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
            $sql = 'SELECT preco_itens.valor_diaria FROM preco_itens WHERE id_equipamento = :id AND id_preco = :id_preco';
            $consulta = $pdo->prepare($sql);
            $consulta->execute([":id" => $id_equipamento, ":id_preco" => $contrato['id_preco']]);
            $equipamento = $consulta->fetch();

            if(!$equipamento){
                $erros[] = "Este equipamento não possui preço cadastrado nesta tabela.";
            }
        }

        if(empty($erros)){
            try {
                $sqlCheck = 'SELECT id, qtd FROM contrato_itens WHERE id_contrato = :id_contrato AND id_equipamento = :id_equipamento';
                $consultaCheck = $pdo->prepare($sqlCheck);
                $consultaCheck->execute([":id_contrato" => $id_contrato, ":id_equipamento" => $id_equipamento]);
                $item_existente = $consultaCheck->fetch();

                if($item_existente){
                    $sql = 'UPDATE contrato_itens SET qtd = qtd + :qtd, diaria = :diaria WHERE id = :id';
                    $stmt = $pdo->prepare($sql);

                    $stmt->execute([
                        ":qtd" => $qtd,
                        ":diaria" => $equipamento['valor_diaria'],
                        ":id" => $item_existente['id']
                    ]);

                    registrarLog("Quantidade do item $id_equipamento atualizado ao contrato: $id_contrato");
                } else {
                    $diaria = $equipamento['valor_diaria'];

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
                }

                header("Location: ver.php?id=$id_contrato");
                exit;
            }
            catch(PDOException $e){
                $erros[] = "Erro ao cadastrar equipamento ao contrato.";
            }
        }
    }

    require_once '../layout/header.php';
?>

<h2>Adicionar Item ao Contrato Nº <?= str_pad($contrato['id'], 4, '0', STR_PAD_LEFT) ?></h2>

<form method="POST">
    <label>Equipamento:</label><br>
    <select name="id_equipamento" required>
        <option value="" disabled <?= empty($id_equipamento) ? 'selected' : '' ?>>- Selecione um equipamento -</option>
        <?php foreach ($equipamentos as $equipamento): ?>
            <option value="<?= e($equipamento['id']) ?>" <?= $id_equipamento == $equipamento['id'] ? 'selected' : '' ?>>
                <?= e($equipamento['descricao']) ?> (R$ <?= number_format($equipamento["valor_diaria"], 2, ',', '.') ?>)
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