<?php
    require_once '../conexao.php';
    require_once '../helpers.php';

    $id = obterId();

    $sql = 'SELECT id, tipo_pessoa, nome, cpf_cnpj, email, telefone, cep, endereco, numero, complemento, bairro, cidade, estado, observacao, created_at FROM clientes WHERE id = :id';
    $consulta = $pdo->prepare($sql);
    $consulta -> execute([':id' => $id]);

    $cliente = $consulta->fetch();

    if(!$cliente){
        die("Cliente não encontrado.");
    }

    require_once '../layout/header.php';
?>

<h2>Detalhes do Cliente <?= $cliente['id'] ?></h2>

<div>
    <p><strong>Tipo de Pessoa:</strong> <?= e($cliente["tipo_pessoa"] == 'F' ? 'Pessoa Física' : 'Pessoa Jurídica') ?></p>
    <p><strong>CPF/CNPJ:</strong> <?= e(formatarCpfCnpj($cliente["cpf_cnpj"])) ?></p>
    <p><strong>Nome:</strong> <?= e($cliente["nome"]) ?></p>
    <p><strong>E-mail:</strong> <?= e($cliente["email"]) ?></p>
    <p><strong>Telefone:</strong> <?= e(formatarTelefone($cliente["telefone"])) ?></p><br>

    <h3>Endereço</h3>
    <p><strong>CEP:</strong> <?= !empty($cliente["cep"]) ? e($cliente["cep"]) : '-' ?></p>
    <p><strong>Endereço:</strong> <?= !empty($cliente["endereco"]) ? e($cliente["endereco"]) : '-' ?></p>
    <p><strong>Número:</strong> <?= !empty($cliente["numero"]) ? e($cliente["numero"]) : '-' ?></p>
    <p><strong>Complemento:</strong> <?= !empty($cliente["complemento"]) ? e($cliente["complemento"]) : '-' ?></p>
    <p><strong>Bairro:</strong> <?= !empty($cliente["bairro"]) ? e($cliente["bairro"]) : '-' ?></p>
    <p><strong>Cidade:</strong> <?= !empty($cliente["cidade"]) ? e($cliente["cidade"]) : '-' ?></p>
    <p><strong>Estado:</strong> <?= !empty($cliente["estado"]) ? e($cliente["estado"]) : '-' ?></p><br>

    <h3>Informações Adicionais</h3>
    <p><strong>Observação:</strong> <?= !empty($cliente["observacao"]) ? e($cliente["observacao"]) : '-' ?></p>
    <p><strong>Cadastrado em:</strong> <?= date('d/m/Y - H:i', strtotime($cliente["created_at"])) ?></p>
</div>

<br><a class="links" href="listar.php">Voltar</a>

<?php require_once '../layout/footer.php'; ?>