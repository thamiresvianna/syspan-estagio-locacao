<?php
    require_once '../conexao.php';
    require_once '../logger.php';
    require_once '../helpers.php';

    $erros = [];
    
    $nome = '';
    $email = '';
    $telefone = '';

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $nome = trim($_POST["nome"] ?? '');
        $email = trim($_POST["email"] ?? '');
        $telefone = trim($_POST["telefone"] ?? '');

        if(strlen($nome) < 3 || strlen($nome) > 120){
            $erros[] = "Nome deve conter entre 3 e 120 caracteres.";
        }
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $erros[] = "E-mail inválido.";
        }
        if($telefone === '' || strlen($telefone) < 10){
            $erros[] = "Telefone inválido.";
        }
        if(empty($erros)){
            $sql = 'INSERT INTO clientes (nome, email, telefone) VALUES (:nome, :email, :telefone)';
            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ":nome" => $nome,
                ":email" => $email,
                ":telefone" => $telefone
            ]);

            registrarLog("Cliente cadastrado: $nome");

            header("Location: listar.php");
            exit;
        }
    }

    require_once '../layout/header.php';
?>

<h2>Inserir Cliente</h2>

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