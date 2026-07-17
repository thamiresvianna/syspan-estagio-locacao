<?php
    require_once '../conexao.php';
    require_once '../logger.php';
    require_once '../helpers.php';

    $sql = 'SELECT id, nome FROM clientes';
    $consulta = $pdo->prepare($sql);
    $consulta->execute();

    $clientes = $consulta->fetchAll();

    $erros = [];

    $id_cliente = '';
    $data_inicio = '';
    $data_fim = '';
    $observacao = '';

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $id_cliente = (int) trim($_POST["id_cliente"] ?? '');
        $data_inicio = trim($_POST["data_inicio"] ?? '');
        $data_fim = trim($_POST["data_fim"] ?? '');
        $observacao = trim($_POST["observacao"] ?? '');

        if($id_cliente <= 0){
            $erros[] = "Cliente inválido.";
        }
        else {
            $sql = 'SELECT id FROM clientes WHERE id = :id';
            $consulta = $pdo->prepare($sql);
            $consulta->execute([':id' => $id_cliente]);

            if(!$consulta->fetch()){
                $erros[] = "Cliente não encontrado.";
            }
        }
        if(empty($data_inicio) || empty($data_fim)){
            $erros[] = "As datas de início e fim são obrigatórias.";
        }
        elseif($data_inicio > $data_fim){
            $erros[] = "A data de início não pode ser maior que a data de fim.";
        }

        if(empty($erros)){
            $status = calcularStatusContrato($data_inicio, $data_fim);

            $sql = 'INSERT INTO contratos (id_cliente, data_inicio, data_fim, status, observacao) 
                    VALUES (:id_cliente, :data_inicio, :data_fim, :status, :observacao)';
            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ":id_cliente" => $id_cliente,
                ":data_inicio" => $data_inicio,
                ":data_fim" => $data_fim,
                ":status" => $status,
                ":observacao" => $observacao
            ]);

            $id_contrato = $pdo->lastInsertId();
            registrarLog("Contrato criado: ID $id_contrato");

            header("Location: listar.php");
            exit;
        }
    }

    require_once '../layout/header.php';
?>

<h2>Novo Contrato</h2>

<form method="POST">
    <label>Cliente:</label><br>
    <select name="id_cliente" required>
        <option value="" disabled <?= empty($id_cliente) ? 'selected' : '' ?>>- Selecione um cliente -</option>
        <?php foreach ($clientes as $cliente): ?>
            <option value="<?= e($cliente['id']) ?>" <?= $id_cliente == $cliente['id'] ? 'selected' : '' ?>>
                <?= e($cliente['nome']) ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <label>Data de Início:</label><br>
    <input type="date" name="data_inicio" value="<?= e($data_inicio ?? '') ?>" required><br>

    <label>Data de Fim:</label><br>
    <input type="date" name="data_fim" value="<?= e($data_fim ?? '') ?>" required><br>

    <label>Observação:</label><br>
    <textarea name="observacao"><?= e($observacao ?? '') ?></textarea><br>

    <button type="submit">Salvar</button>
    <a class="botao-cancelar" href="listar.php">Cancelar</a>
</form>

<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($erros)) {
        mostrarErros($erros);
    }
?>

<?php require_once '../layout/footer.php'; ?>