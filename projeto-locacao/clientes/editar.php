<?php
    require_once '../conexao.php';
    require_once '../logger.php';
    require_once '../helpers.php';

    $erros = [];
    $id = obterId();

    $sql = 'SELECT id, nome, email, telefone, created_at FROM clientes WHERE id = :id';
    $consulta = $pdo->prepare($sql);
    $consulta -> execute([':id' => $id]);

    $cliente = $consulta->fetch();

    if(!$cliente){
        die("Cliente não encontrado.");
    }

    $nome = $cliente['nome'];
    $email = $cliente['email'];
    $telefone = $cliente['telefone'];

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $nome = trim($_POST["nome"] ?? '');
        $email = trim($_POST["email"] ?? '');
        $telefone = trim($_POST["telefone"] ?? '');

        $erros = validarCliente($nome, $email, $telefone);
        
        if(empty($erros)){
            $sql = 'UPDATE clientes SET nome = :nome, email = :email, telefone = :telefone WHERE id = :id';
            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ":nome" => $nome,
                ":email" => $email,
                ":telefone" => $telefone,
                ":id" => $id
            ]);

            registrarLog("Cliente editado: ID $id");

            header("Location: listar.php");
            exit;
        }
    }

    require_once '../layout/header.php';
?>

<h2>Editar Cliente</h2>

<form method="POST">
    <label>Nome:</label><br>
    <input type="text" name="nome" value="<?= e($nome ?? '') ?>" required><br>

    <label>E-mail:</label><br>
    <input type="email" name="email" value="<?= e($email ?? '') ?>" required><br>

    <label>Telefone:</label><br>
    <input type="text" name="telefone" value="<?= e($telefone ?? '') ?>" required><br><br>

    <button type="submit">Salvar</button>
    <a class="botao-cancelar" href="listar.php">Cancelar</a>
</form>

<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($erros)) {
        mostrarErros($erros);
    }
?>

<?php require_once '../layout/footer.php'; ?>