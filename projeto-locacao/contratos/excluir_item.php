<?php
    require_once '../conexao.php';
    require_once '../logger.php';
    require_once '../helpers.php';

    $id_item = obterId();

    $sql = 'SELECT contrato_itens.id, contrato_itens.id_contrato, contrato_itens.id_equipamento, 
            contrato_itens.diaria, contrato_itens.qtd, equipamentos.descricao FROM contrato_itens
            INNER JOIN equipamentos ON equipamentos.id = contrato_itens.id_equipamento 
            WHERE contrato_itens.id = :id';
    $consulta = $pdo->prepare($sql);
    $consulta->execute([':id' => $id_item]);

    $item = $consulta->fetch();

    if(!$item){
        die("Item não encontrado.");
    }

    $sql = 'SELECT id, data_inicio, data_fim FROM contratos WHERE id = :id';
    $consulta = $pdo->prepare($sql);
    $consulta->execute([':id' => $item['id_contrato']]);
    $contrato = $consulta->fetch();

    if(!$contrato){
        die("Contrato não encontrado.");
    }

    $status = calcularStatusContrato($contrato['data_inicio'], $contrato['data_fim']);

    if($status == "ENCERRADO"){
        die("Não é possível excluir itens de um contrato encerrado.");
    }

    $erro = '';

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        try{
            $sql = 'DELETE FROM contrato_itens WHERE id = :id';
            $stmt = $pdo->prepare($sql);

            $stmt->execute([":id" => $id_item]);

            if($stmt->rowCount() === 0){
                $erro = "Erro ao excluir item.";
            }

            registrarLog("Item do contrato excluído: ID $id_item");

            header("Location: ver.php?id=".$item['id_contrato']);
            exit;
        }
        catch(PDOException $e){
            $erro = "Erro ao excluir item.";
        }
    }

    require_once '../layout/header.php';
?>

<?php 
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($erro)){
        mostrarErros([$erro]);
    }
?>

<h2>Excluir Item do Contrato Nº <?= str_pad($item['id_contrato'], 4, '0', STR_PAD_LEFT) ?></h2>

<p>Tem certeza que deseja excluir o item: <strong><?= e($item["descricao"]) ?></strong> deste contrato?</p>

<form method="POST">
    <button type="submit">Excluir</button>
    <a class="botao-cancelar" href="ver.php?id=<?= (int)$item['id_contrato'] ?>">Cancelar</a>
</form>

<?php require_once '../layout/footer.php'; ?>