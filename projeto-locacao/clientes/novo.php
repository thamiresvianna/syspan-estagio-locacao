<?php
    require_once '../conexao.php';
    require_once '../logger.php';
    require_once '../helpers.php';

    $erros = [];
    
    $tipo_pessoa = 'F';
    $nome = '';
    $cpf_cnpj = '';
    $email = '';
    $telefone = '';
    $cep = '';
    $endereco = '';
    $numero = '';
    $complemento = '';
    $bairro = '';
    $cidade = '';
    $estado = '';
    $observacao = '';

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $tipo_pessoa = trim($_POST["tipo_pessoa"] ?? '');
        $nome = trim($_POST["nome"] ?? '');
        $cpf_cnpj = trim($_POST["cpf_cnpj"] ?? '');
        $email = trim($_POST["email"] ?? '');
        $telefone = trim($_POST["telefone"] ?? '');
        $cep = trim($_POST["cep"] ?? '');
        $endereco = trim($_POST["endereco"] ?? '');
        $numero = trim($_POST["numero"] ?? '');
        $complemento = trim($_POST["complemento"] ?? '');
        $bairro = trim($_POST["bairro"] ?? '');
        $cidade = trim($_POST["cidade"] ?? '');
        $estado = strtoupper(trim($_POST["estado"] ?? ''));
        $observacao = trim($_POST["observacao"] ?? '');

        $erros = validarCliente($tipo_pessoa, $nome, $cpf_cnpj, $email, $telefone, $cep, $endereco, $numero, $complemento, $bairro, $cidade, $estado, $observacao);
        
        if(empty($erros)){
            $sql = 'SELECT id FROM clientes WHERE email = :email';
            $consulta = $pdo->prepare($sql);
            $consulta->execute([":email" => $email]);

            if($consulta->fetchColumn()){
                $erros[] = "Já existe um cliente com esse e-mail.";
            }

            $sql = 'SELECT id FROM clientes WHERE cpf_cnpj = :cpf_cnpj';
            $consulta = $pdo->prepare($sql);
            $consulta->execute([":cpf_cnpj" => $cpf_cnpj]);

            if($consulta->fetchColumn()){
                $erros[] = "Já existe um cliente com esse CPF/CNPJ.";
            }
        }

        if(empty($erros)){
            try{
                $sql = 'INSERT INTO clientes (tipo_pessoa, nome, cpf_cnpj, email, telefone, cep, endereco, numero, complemento, bairro, cidade, estado, observacao) 
                        VALUES (:tipo_pessoa, :nome, :cpf_cnpj, :email, :telefone, :cep, :endereco, :numero, :complemento, :bairro, :cidade, :estado, :observacao)';
                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    ":tipo_pessoa" => $tipo_pessoa,
                    ":nome" => $nome,
                    ":cpf_cnpj" => $cpf_cnpj,
                    ":email" => $email,
                    ":telefone" => $telefone,
                    ":cep" => $cep,
                    ":endereco" => $endereco,
                    ":numero" => $numero,
                    ":complemento" => $complemento,
                    ":bairro" => $bairro,
                    ":cidade" => $cidade,
                    ":estado" => $estado,
                    ":observacao" => $observacao,
                ]);

                registrarLog("Cliente cadastrado: $nome");

                header("Location: listar.php");
                exit;
            } 
            catch(PDOException $e){
                $erros[] = "Erro ao salvar cliente.";
            }
        }
    }

    require_once '../layout/header.php';
?>

<h2>Inserir Cliente</h2>

<form method="POST" class="form-grid">
    <fieldset>
        <legend>Dados Pessoais</legend>
        <div class="grid-campos">
            <div class="campo-form">
                <label>Tipo Pessoa:</label><br>
                <select name="tipo_pessoa" required>
                    <option value="F" <?= $tipo_pessoa == 'F' ? 'selected' : '' ?>>Pessoa Física</option>
                    <option value="J" <?= $tipo_pessoa == 'J' ? 'selected' : '' ?>>Pessoa Jurídica</option>
                </select><br>
            </div>

            <div class="campo-form">
                <label>CPF/CNPJ:</label><br>
                <input type="text" name="cpf_cnpj" value="<?= e($cpf_cnpj ?? '') ?>" required><br>
            </div>

            <div class="campo-form">
                <label>Nome:</label><br>
                <input type="text" name="nome" value="<?= e($nome ?? '') ?>" required><br>
            </div>

            <div class="campo-form">
                <label>E-mail:</label><br>
                <input type="email" name="email" value="<?= e($email ?? '') ?>" required><br>
            </div>

            <div class="campo-form">
                <label>Telefone:</label><br>
                <input type="text" name="telefone" value="<?= e($telefone ?? '') ?>" required><br>
            </div>
        </div>
    </fieldset>
    
    <fieldset>
        <legend>Endereço</legend>
        <div class="grid-campos">
            <div class="campo-form">
                <label>CEP:</label><br>
                <input type="text" name="cep" value="<?= e($cep ?? '') ?>" required><br>
            </div>
            
            <div class="campo-form">
                <label>Endereço:</label><br>
                <input type="text" name="endereco" value="<?= e($endereco ?? '') ?>" required><br>
            </div>

            <div class="campo-form">
                <label>Nº:</label><br>
                <input type="text" name="numero" value="<?= e($numero ?? '') ?>" required><br>
            </div>
            
            <div class="campo-form">
                <label>Complemento:</label><br>
                <input type="text" name="complemento" value="<?= e($complemento ?? '') ?>"><br>
            </div>
            
            <div class="campo-form">
                <label>Bairro:</label><br>
                <input type="text" name="bairro" value="<?= e($bairro ?? '') ?>" required><br>
            </div>
            
            <div class="campo-form">
                <label>Cidade:</label><br>
                <input type="text" name="cidade" value="<?= e($cidade ?? '') ?>" required><br>
            </div>

            <div class="campo-form">
                <label>Estado:</label><br>
                <input type="text" maxlength="2" name="estado" value="<?= e($estado ?? '') ?>" required><br>
            </div>
        </div>
    </fieldset>
    
    <fieldset>
        <legend>Informações Adicionais</legend>
        <div class="campo">
            <label>Observação:</label><br>
            <textarea name="observacao"><?= e($observacao ?? '') ?></textarea><br><br>
        </div>
    </fieldset>

    <div class="botoes-acoes">
        <button type="submit">Salvar</button>
        <a class="botao-cancelar" href="listar.php">Cancelar</a>
    </div>
</form>

<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($erros)) {
        mostrarErros($erros);
    }
?>

<?php require_once '../layout/footer.php'; ?>