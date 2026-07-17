<?php
    require_once '../conexao.php';
    require_once '../logger.php';

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
            $data_hoje = date('Y-m-d');

            if ($data_hoje < $data_inicio) {
                $status = "AGENDADO";
            } elseif ($data_hoje >= $data_inicio && $data_hoje <= $data_fim) {
                $status = "ATIVO";
            } else {
                $status = "ENCERRADO";
            }

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
        <?php foreach ($clientes as $cliente): ?>
            <option value="<?= htmlspecialchars($cliente['id']) ?>"><?= htmlspecialchars($cliente['nome']) ?></option>
        <?php endforeach; ?>
    </select><br>

    <label>Data de Início:</label><br>
    <input type="date" name="data_inicio" value="<?= htmlspecialchars($data_inicio ?? '') ?>" required><br>

    <label>Data de Fim:</label><br>
    <input type="date" name="data_fim" value="<?= htmlspecialchars($data_fim ?? '') ?>" required><br>

    <label>Observação:</label><br>
    <textarea name="observacao"><?= htmlspecialchars($observacao ?? '') ?></textarea><br>

    <button type="submit">Salvar</button>
</form>

<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($erros)) {
        foreach ($erros as $erro){
            echo "<p class='erro'>$erro</p>";
        }
    }
?>

<?php require_once '../layout/footer.php'; ?>