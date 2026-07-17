<?php
    require_once '../conexao.php';
    require_once '../helpers.php';

    $id = obterId();

    $sql = 'SELECT contratos.id, clientes.nome AS cliente, contratos.data_inicio, contratos.data_fim, contratos.status, contratos.observacao, contratos.created_at 
            FROM contratos INNER JOIN clientes ON contratos.id_cliente = clientes.id
            WHERE contratos.id = :id';

    $consulta = $pdo->prepare($sql);
    $consulta->execute([':id' => $id]);
    $contrato = $consulta->fetch();

    if(!$contrato){
        die("Contrato não encontrado.");
    }

    $status_atual = calcularStatusContrato($contrato['data_inicio'], $contrato['data_fim']);

    $total_dias = calcularTotalDias($contrato['data_inicio'], $contrato['data_fim']);

    $sqlItens = 'SELECT contrato_itens.id, contrato_itens.id_contrato, equipamentos.descricao AS equipamento, contrato_itens.diaria, contrato_itens.qtd
                FROM contrato_itens INNER JOIN equipamentos ON contrato_itens.id_equipamento = equipamentos.id
                WHERE contrato_itens.id_contrato = :id';
    $consultaItens = $pdo->prepare($sqlItens);
    $consultaItens->execute([':id' => $id]);
    $contratoItens = $consultaItens->fetchAll();

    require_once '../layout/header.php';
?>

<h2>Detalhes do Contrato <?= $contrato['id'] ?></h2>

<div>
    <p><strong>Cliente:</strong> <?= e($contrato["cliente"]) ?></p>
    <p><strong>Período:</strong> <?= date('d/m/Y', strtotime($contrato["data_inicio"])) ?> até <?= date('d/m/Y', strtotime($contrato["data_fim"])) ?> </p>
    <p><strong>Duração:</strong> <?= $total_dias ?> dias</p>
    <p><strong>Status:</strong> <?= e($status_atual) ?></p>
    <p><strong>Observação:</strong> <?= !empty($contrato["observacao"]) ? e($contrato["observacao"]) : '-' ?></p>    
</div>

<br><a class="links" href="listar.php">Voltar para listagem</a>
<a class="links" href="adicionar_item.php?id=<?= (int)$contrato['id'] ?>">Adicionar Equipamento</a><br><br><hr>

<h3>Itens do Contrato</h3>

<?php if(!empty($contratoItens)): ?>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID do Item</th>
            <th>Equipamento</th>
            <th>Valor Diária Unitária</th>
            <th>Quantidade</th>
            <th>Subtotal por Dia</th>
            <th>Total do Período</th>
        </tr>

        <?php
            $valor_total = 0;
            foreach($contratoItens as $row): 
                $subtotal = $row["diaria"] * $row["qtd"];
                $total_periodo = $subtotal * $total_dias;
                $valor_total += $total_periodo;
        ?>
            <tr>
                <td><?= (int)$row["id"] ?></td>
                <td><?= e($row["equipamento"]) ?></td>
                <td>R$ <?= number_format($row["diaria"], 2, ',', '.') ?></td>
                <td><?= (int)$row["qtd"] ?></td>
                <td>R$ <?= number_format($subtotal, 2, ',', '.') ?></td>
                <td>R$ <?= number_format($total_periodo, 2, ',', '.') ?></td>
            </tr>
        <?php endforeach; ?>

        <tr>
            <td colspan="5"><strong>Total do Contrato:</strong></td>
            <td><strong>R$ <?= number_format($valor_total, 2, ',', '.') ?></strong></td>
        </tr>
    </table>

<?php else: ?>
    <p>Nenhum equipamento foi registrado para este contrato ainda.</p>
<?php endif; ?>

<?php require_once '../layout/footer.php'; ?>